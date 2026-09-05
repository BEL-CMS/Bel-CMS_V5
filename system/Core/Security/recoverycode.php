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

final class RecoveryCode
{
    /**
     * Caractères autorisés
     */
    private const CHARS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * Génère un code
     */
    public static function generate(int $length = 12): string
    {
        $chars = self::CHARS;
        $max   = strlen($chars) - 1;

        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, $max)];
        }

        return substr($code, 0, 4)
            . '-'
            . substr($code, 4, 4)
            . '-'
            . substr($code, 8, 4);
    }

    /**
     * Génère plusieurs codes
     */
    public static function generateList(int $number = 10): array
    {
        $codes = [];

        while (count($codes) < $number) {

            $code = self::generate();

            $codes[$code] = true; // évite les doublons
        }

        return array_keys($codes);
    }
}