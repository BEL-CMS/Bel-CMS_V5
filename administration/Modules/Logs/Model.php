<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */

declare(strict_types=1);

namespace BelCMS\Admin\Logs;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

use BelCMS\Core\BDD;
use InvalidArgumentException;

final class Model
{
    public function __construct(private BDD $db) {
    }

	public function getAllLogs () : array|bool
	{
		$this->db->table('TABLE_LOGS');
        $this->db->orderby([['name' => 'id','type' => 'DESC',]]);
		$this->db->queryAll();
		return $this->db->data;
	}

	public function addban (array $data) : bool
	{
		$this->db->table('TABLE_BANISHMENT');
		$this->db->insert($data);
		$this->db->data;
		if ($this->db->data === true) {
			return true;
		} else {
			return false;
		}
	}

    public function isBanned(?string $hashKey = null,?string $ipAddress = null): bool
	{
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
}