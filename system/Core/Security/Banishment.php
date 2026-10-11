<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/

declare(strict_types=1);

namespace BelCMS\Core\Security;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

use BelCMS\Core\BDD;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

final class Banishment
{
    /**
     * Barème des sanctions.
     * Le niveau 0 correspond à un bannissement définitif.
     */
    private const DURATIONS = [
        9 => ['minutes' => 1],
        8 => ['minutes' => 5],
        7 => ['minutes' => 15],
        6 => ['hours'   => 1],
        5 => ['days'    => 1],
        4 => ['days'    => 7],
        3 => ['months'  => 1],
        2 => ['months'  => 6],
        1 => ['years'   => 1],
        0 => null,
    ];

    public function __construct(
        private readonly BDD $db
    ) {
    }

    /**
     * Retourne la date d'expiration en UTC.
     * NULL = sanction définitive.
     */
    private function expirationDate(int $level): ?string
    {
        if (!array_key_exists($level, self::DURATIONS)) {
            throw new InvalidArgumentException(
                'Niveau de bannissement invalide.'
            );
        }

        if ($level === 0) {
            return null;
        }

        $duration = self::DURATIONS[$level];

        $unit = array_key_first($duration);
        $amount = $duration[$unit];

        $date = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        return $date
            ->modify(sprintf('+%d %s', $amount, $unit))
            ->format('Y-m-d H:i:s');
    }

    /**
     * Vérifie si un compte ou une IP est banni.
     *
     * Les deux critères sont combinés avec OR :
     * un bannissement du compte OU de l'IP suffit.
     */
    public function isBanned(
        ?string $hashKey = null,
        ?string $ipAddress = null
    ): bool {
        $hashKey = $this->normalizeHashKey($hashKey);
        $ipAddress = $this->normalizeIp($ipAddress);

        if ($hashKey === null && $ipAddress === null) {
            return false;
        }

        // Une sanction révoquée n'est plus active.
        $this->db
            ->table('TABLE_BANISHMENT')
            ->fields(['id'])
            ->where([
                [
                    'name'  => 'revoked_at',
                    'value' => null,
                    'op'    => 'IS NULL',
                ],
            ]);

        // Cible : compte ou adresse IP.
        $targets = [];

        if ($hashKey !== null) {
            $targets[] = [
                'name'  => 'hash_key',
                'value' => $hashKey,
                'op'    => '=',
            ];
        }

        if ($ipAddress !== null) {
            $targets[] = [
                'name'  => 'ip_address',
                'value' => $ipAddress,
                'op'    => '=',
            ];
        }

        $this->db->whereOr($targets);

        // Une sanction sans échéance est définitive.
        // Une échéance future signifie que la sanction est active.
        $this->db->whereOr([
            [
                'name'  => 'expires_at',
                'value' => null,
                'op'    => 'IS NULL',
            ],
            [
                'name'  => 'expires_at',
                'value' => gmdate('Y-m-d H:i:s'),
                'op'    => '>',
            ],
        ]);

        return $this->db->queryOne() !== false;
    }

    /**
     * Crée un bannissement.
     *
     * Retourne l'identifiant créé, ou false en cas d'échec
     * d'insertion.
     */
    public function ban(?string $hashKey,?string $ipAddress,string $reason,int $level,?string $adminHashKey = null): int|false
    {
        $hashKey      = $this->normalizeHashKey($hashKey);
        $ipAddress    = $this->normalizeIp($ipAddress);
        $adminHashKey = $this->normalizeHashKey($adminHashKey);

        $reason = trim($reason);

        if ($hashKey === null && $ipAddress === null) {
            throw new InvalidArgumentException(
                'Une cible est obligatoire : compte ou IP.'
            );
        }

        if ($reason === '') {
            throw new InvalidArgumentException(
                'Le motif du bannissement est obligatoire.'
            );
        }

        if (!array_key_exists($level, self::DURATIONS)) {
            throw new InvalidArgumentException(
                'Niveau de bannissement invalide.'
            );
        }

        $reason = mb_substr($reason, 0, 500);
        $now = gmdate('Y-m-d H:i:s');
        $expiresAt = $this->expirationDate($level);

        // Référence publique de 32 caractères communiquée par l'utilisateur.
        $banishmentKey = bin2hex(random_bytes(16));

        $inserted = $this->db
            ->table('TABLE_BANISHMENT')
            ->insert([
                'banishment_key' => $banishmentKey,
                'hash_key'       => $hashKey,
                'ip_address'   => $ipAddress,
                'reason'       => $reason,
                'level'        => $level,
                'created_by'   => $adminHashKey,
                'created_at'   => $now,
                'expires_at'   => $expiresAt,
                'revoked_at'   => null,
                'revoked_by'   => null,
                'revoke_reason' => null,
            ]);

        if (!$inserted) {
            return false;
        }

        $id = (int) $this->db->lastId();

        if ($id <= 0) {
            return false;
        }

        // Journal de création.
        $this->db
            ->table('TABLE_BANISHMENT_LOGS')
            ->insert([
                'banishment_id'  => $id,
                'target_hash_key' => $hashKey,
                'target_ip'       => $ipAddress,
                'admin_hash_key'  => $adminHashKey,
                'action'          => 'ban',
                'reason'          => $reason,
                'created_at'      => $now,
            ]);

        return $id;
    }

    /**
     * Lève une sanction sans supprimer son historique.
     */
    public function revoke(
        int $id,
        ?string $adminHashKey = null,
        string $reason = 'Levée manuelle'
    ): bool {
        if ($id <= 0) {
            return false;
        }

        $adminHashKey = $this->normalizeHashKey($adminHashKey);
        $reason = mb_substr(trim($reason), 0, 500);

        $banishment = $this->db
            ->table('TABLE_BANISHMENT')
            ->where([
                [
                    'name'  => 'id',
                    'value' => $id,
                    'op'    => '=',
                ],
                [
                    'name'  => 'revoked_at',
                    'value' => null,
                    'op'    => 'IS NULL',
                ],
            ])
            ->queryOne();

        if (!$banishment) {
            return false;
        }

        $now = gmdate('Y-m-d H:i:s');

        $updated = $this->db
            ->table('TABLE_BANISHMENT')
            ->where([
                [
                    'name'  => 'id',
                    'value' => $id,
                    'op'    => '=',
                ],
                [
                    'name'  => 'revoked_at',
                    'value' => null,
                    'op'    => 'IS NULL',
                ],
            ])
            ->update([
                'revoked_at'   => $now,
                'revoked_by'   => $adminHashKey,
                'revoke_reason' => $reason,
            ]);

        if (!$updated) {
            return false;
        }

        $this->db
            ->table('TABLE_BANISHMENT_LOGS')
            ->insert([
                'banishment_id'  => $id,
                'target_hash_key' => $banishment->hash_key,
                'target_ip'       => $banishment->ip_address,
                'admin_hash_key'  => $adminHashKey,
                'action'          => 'revoke',
                'reason'          => $reason,
                'created_at'      => $now,
            ]);

        return true;
    }

    /**
     * Normalise et valide le hash_key.
     */
    private function normalizeHashKey(?string $hashKey): ?string
    {
        if ($hashKey === null || trim($hashKey) === '') {
            return null;
        }

        $hashKey = trim($hashKey);

        // Bel-CMS utilise des identifiants alphanumériques.
        if (!preg_match('/^[a-zA-Z0-9]{32}$/', $hashKey)) {
            throw new InvalidArgumentException(
                'Identifiant hash_key invalide.'
            );
        }

        return $hashKey;
    }

    /**
     * Valide une adresse IPv4 ou IPv6.
     */
    private function normalizeIp(
        ?string $ipAddress
    ): ?string {
        $ipAddress = trim((string) $ipAddress);

        if ($ipAddress === '') {
            return null;
        }

        if (filter_var(
            $ipAddress,
            FILTER_VALIDATE_IP
        ) === false) {
            throw new InvalidArgumentException(
                'Adresse IP invalide.'
            );
        }

        return $ipAddress;
    }
    /**
     * Récupère les détails d'un bannissement actif.
     *
     * Recherche d'abord par compte, puis par adresse IP.
     *
     * @return object|null
     */
    public function findActive(
        ?string $hashKey = null,
        ?string $ipAddress = null
    ): ?object {
        $hashKey = $this->normalizeHashKey($hashKey);
        $ipAddress = $this->normalizeIp($ipAddress);

        $now = gmdate('Y-m-d H:i:s');

        $targets = [];

        if ($hashKey !== null) {
            $targets[] = [
                'name'  => 'hash_key',
                'value' => $hashKey,
            ];
        }

        if ($ipAddress !== null) {
            $targets[] = [
                'name'  => 'ip_address',
                'value' => $ipAddress,
            ];
        }

        foreach ($targets as $target) {
            $this->db->table('TABLE_BANISHMENT');

            $this->db->where([
                [
                    'name'  => 'revoked_at',
                    'value' => null,
                    'op'    => 'IS NULL',
                ],
                $target,
            ]);

            $this->db->whereOr([
                [
                    'name'  => 'expires_at',
                    'value' => null,
                    'op'    => 'IS NULL',
                ],
                [
                    'name'  => 'expires_at',
                    'value' => $now,
                    'op'    => '>',
                ],
            ]);

            $result = $this->db->queryOne();

            if (is_object($result)) {
                return $result;
            }
        }

        return null;
    }
}
