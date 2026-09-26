<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */
declare(strict_types=1);

namespace BelCMS\Admin\User;
use BelCMS\Core\BDD;
final class Model
{
    public function __construct(
        private BDD $db
    ) {
    }
    /**
     * Liste des utilisateurs.
     */
    public function getUsers(
        int $page = 1,
        int $perPage = 25,
        string $search = ''
    ): array {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;
        $this->db->table(TABLE_USERS);
        if ($search !== '') {
            $this->db->whereLike('username', $search);
        }
        $this->db->orderby('id');
        $this->db->limit([$offset, $perPage], true);
        $this->db->queryAll();
        if (!is_array($this->db->data)) {
            return [];
        }
        return $this->db->data;
    }
    /**
     * Nombre total d'utilisateurs.
     */
    public function countUsers(
        string $search = ''
    ): int {
        $this->db->table(TABLE_USERS);
        if ($search !== '') {
            $this->db->whereLike('username', $search);
        }
        $this->db->count();
        return (int) $this->db->data;
    }
    /**
     * Récupère un utilisateur par son hash_key.
     */
    public function getUserByHashKey(
        string $hashKey
    ): ?object {
        $hashKey = trim($hashKey);
        if ($hashKey === '') {
            return null;
        }
        $this->db->table(TABLE_USERS);
        $this->db->where([
            [
                'name'  => 'hash_key',
                'value' => $hashKey,
            ],
        ]);
        $this->db->queryOne();
        return is_object($this->db->data)
            ? $this->db->data
            : null;
    }
    /**
     * Récupère le profil utilisateur.
     */
    public function getProfileByHashKey(
        string $hashKey
    ): ?object {
        $hashKey = trim($hashKey);
        if ($hashKey === '') {
            return null;
        }
        $this->db->table(TABLE_USERS_PROFILS);
        $this->db->where([
            [
                'name'  => 'hash_key',
                'value' => $hashKey,
            ],
        ]);
        $this->db->queryOne();
        return is_object($this->db->data)
            ? $this->db->data
            : null;
    }
    /**
     * Récupère les groupes utilisateur.
     */
    public function getGroupsByHashKey(
        string $hashKey
    ): ?object {
        $hashKey = trim($hashKey);
        if ($hashKey === '') {
            return null;
        }
        $this->db->table(TABLE_USERS_GROUPS);
        $this->db->where([
            [
                'name'  => 'hash_key',
                'value' => $hashKey,
            ],
        ]);
        $this->db->queryOne();
        return is_object($this->db->data)
            ? $this->db->data
            : null;
    }
    /**
     * Met à jour le compte utilisateur.
     */
    public function updateUser(
        string $hashKey,
        array $data
    ): bool {
        $hashKey = trim($hashKey);
        if ($hashKey === '' || $data === []) {
            return false;
        }
        $this->db->table(TABLE_USERS);
        $this->db->where([
            [
                'name'  => 'hash_key',
                'value' => $hashKey,
            ],
        ]);
        $this->db->update($data);
        return true;
    }
    /**
     * Réinitialise le 2FA d'un utilisateur.
     */
    public function resetTwoFactor(string $hashKey): bool
    {
        $hashKey = trim($hashKey);
        if ($hashKey === '') {
            return false;
        }
        $this->db->table(TABLE_USERS);
        $this->db->where([
            [
                'name'  => 'hash_key',
                'value' => $hashKey,
            ],
        ]);
        $this->db->update([
            'two_factor_enabled' => 0,
            'two_factor_secret'  => null,
            'login_2fa'          => 0,
            'serial_secure'      => null,
        ]);
        return true;
    }
    public function deleteUser(string $hashKey): bool
    {
        if ($hashKey === '') {
            return false;
        }
        /*
        * Supprimer le profil.
        */
        $this->db->table(TABLE_USERS_PROFILS);
        $this->db->where([
            [
                'name' => 'hash_key',
                'value' => $hashKey
            ]
        ]);
        $this->db->delete();
        /*
        * Supprimer les réseaux sociaux.
        */
        $this->db->table(TABLE_USERS_SOCIAL);
        $this->db->where([
            [
                'name' => 'hash_key',
                'value' => $hashKey
            ]
        ]);
        $this->db->delete();
        /*
        * Supprimer les groupes.
        */
        $this->db->table(TABLE_USERS_GROUPS);
        $this->db->where([
            [
                'name' => 'hash_key',
                'value' => $hashKey
            ]
        ]);
        $this->db->delete();
        /*
        * Supprimer les codes de récupération.
        */
        $this->db->table(TABLE_RECOVERY);
        $this->db->where([
            [
                'name' => 'hash_key',
                'value' => $hashKey
            ]
        ]);
        $this->db->delete();
        /*
        * Supprimer l'utilisateur.
        */
        $this->db->table(TABLE_USERS);
        $this->db->where([
            [
                'name' => 'hash_key',
                'value' => $hashKey
            ]
        ]);
        $this->db->delete();
        return true;
    }
}