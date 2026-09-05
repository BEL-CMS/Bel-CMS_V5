<?php
/**
 * Bel-CMS [Content management system]
 * @version 4.2.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @link https://determe.be
 * @license MIT License
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/

declare(strict_types=1);

namespace BelCMS\Core\Security\TOTP;

/**
 * Générateur et vérificateur TOTP compatible RFC 6238.
 */
final class TOTP
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Génère un secret Base32 cryptographiquement sûr.
     *
     * 20 octets produisent un secret Base32 de 32 caractères,
     * valeur recommandée pour SHA-1 et Google Authenticator.
     */
    public static function generateSecret(int $bytes = 20): string
    {
        if ($bytes < 10 || $bytes > 128) {
            throw new \InvalidArgumentException(
                'La taille du secret doit être comprise entre 10 et 128 octets.'
            );
        }

        return self::encodeBase32(random_bytes($bytes));
    }

    /**
     * Génère le code TOTP correspondant à un instant donné.
     */
    public static function generate(
        string $secret,
        ?int $timestamp = null,
        int $period = 30,
        int $digits = 6,
        string $algorithm = 'sha1'
    ): string {
        if ($period <= 0) {
            throw new \InvalidArgumentException('La période doit être supérieure à zéro.');
        }

        $timestamp ??= time();
        $counter = intdiv($timestamp, $period);

        return self::generateFromCounter(
            secret: $secret,
            counter: $counter,
            digits: $digits,
            algorithm: $algorithm
        );
    }

    /**
     * Vérifie un code TOTP dans une fenêtre temporelle donnée.
     *
     * Une fenêtre de 1 accepte le code précédent, courant et suivant.
     */
    public static function verify(
        string $secret,
        string|int $code,
        int $window = 1,
        ?int $timestamp = null,
        int $period = 30,
        int $digits = 6,
        string $algorithm = 'sha1'
    ): bool {
        if ($window < 0 || $window > 20) {
            throw new \InvalidArgumentException('La fenêtre doit être comprise entre 0 et 20.');
        }

        if ($period <= 0) {
            throw new \InvalidArgumentException('La période doit être supérieure à zéro.');
        }

        self::assertDigits($digits);

        $code = trim((string) $code);
        if (!preg_match('/^\d{' . $digits . '}$/D', $code)) {
            return false;
        }

        $timestamp ??= time();
        $counter = intdiv($timestamp, $period);

        for ($offset = -$window; $offset <= $window; $offset++) {
            $candidateCounter = $counter + $offset;
            if ($candidateCounter < 0) {
                continue;
            }

            $candidate = self::generateFromCounter(
                secret: $secret,
                counter: $candidateCounter,
                digits: $digits,
                algorithm: $algorithm
            );

            if (hash_equals($candidate, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Génère un HOTP à partir d'un compteur, utilisé par TOTP.
     */
    public static function generateFromCounter(
        string $secret,
        int $counter,
        int $digits = 6,
        string $algorithm = 'sha1'
    ): string {
        if ($counter < 0) {
            throw new \InvalidArgumentException('Le compteur ne peut pas être négatif.');
        }

        self::assertDigits($digits);
        $algorithm = self::normalizeAlgorithm($algorithm);
        $binarySecret = self::decodeBase32($secret);

        if ($binarySecret === '') {
            throw new \InvalidArgumentException('Le secret TOTP ne peut pas être vide.');
        }

        $high = intdiv($counter, 0x100000000);
        $low = $counter % 0x100000000;
        $counterBytes = pack('N2', $high, $low);

        $hash = hash_hmac($algorithm, $counterBytes, $binarySecret, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;

        $binaryCode =
            ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);

        $modulo = 10 ** $digits;
        return str_pad((string) ($binaryCode % $modulo), $digits, '0', STR_PAD_LEFT);
    }

    /**
     * Décode un secret Base32 en données binaires.
     */
    public static function decodeBase32(string $secret): string
    {
        $secret = strtoupper($secret);
        $secret = preg_replace('/[\s\-=]+/', '', $secret) ?? '';

        if ($secret === '') {
            return '';
        }

        $buffer = 0;
        $bitsInBuffer = 0;
        $output = '';

        $length = strlen($secret);
        for ($i = 0; $i < $length; $i++) {
            $value = strpos(self::BASE32_ALPHABET, $secret[$i]);
            if ($value === false) {
                throw new \InvalidArgumentException(
                    sprintf('Caractère Base32 invalide à la position %d.', $i)
                );
            }

            $buffer = ($buffer << 5) | $value;
            $bitsInBuffer += 5;

            while ($bitsInBuffer >= 8) {
                $bitsInBuffer -= 8;
                $output .= chr(($buffer >> $bitsInBuffer) & 0xFF);

                // Ne conserve que les bits restant réellement utiles.
                if ($bitsInBuffer === 0) {
                    $buffer = 0;
                } else {
                    $buffer &= (1 << $bitsInBuffer) - 1;
                }
            }
        }

        return $output;
    }

    /**
     * Encode des données binaires en Base32 sans remplissage "=".
     */
    public static function encodeBase32(string $binary): string
    {
        if ($binary === '') {
            return '';
        }

        $buffer = 0;
        $bitsInBuffer = 0;
        $output = '';

        $length = strlen($binary);
        for ($i = 0; $i < $length; $i++) {
            $buffer = ($buffer << 8) | ord($binary[$i]);
            $bitsInBuffer += 8;

            while ($bitsInBuffer >= 5) {
                $bitsInBuffer -= 5;
                $output .= self::BASE32_ALPHABET[($buffer >> $bitsInBuffer) & 0x1F];

                if ($bitsInBuffer === 0) {
                    $buffer = 0;
                } else {
                    $buffer &= (1 << $bitsInBuffer) - 1;
                }
            }
        }

        if ($bitsInBuffer > 0) {
            $output .= self::BASE32_ALPHABET[($buffer << (5 - $bitsInBuffer)) & 0x1F];
        }

        return $output;
    }

    private static function assertDigits(int $digits): void
    {
        if ($digits < 6 || $digits > 10) {
            throw new \InvalidArgumentException(
                'Le nombre de chiffres doit être compris entre 6 et 10.'
            );
        }
    }

    private static function normalizeAlgorithm(string $algorithm): string
    {
        $algorithm = strtolower(trim($algorithm));

        if (!in_array($algorithm, ['sha1', 'sha256', 'sha512'], true)) {
            throw new \InvalidArgumentException(
                'Algorithme non pris en charge. Utilisez SHA1, SHA256 ou SHA512.'
            );
        }

        return $algorithm;
    }
}
