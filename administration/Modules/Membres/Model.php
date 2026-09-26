<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */
declare(strict_types=1);

namespace BelCMS\Admin\Membres;
use BelCMS\Core\BDD;
final class Model
{
    /**
     * Instance BDD.
     */
    private BDD $db;
    /**
     * Constructeur.
     */
    public function __construct(BDD $db)
    {
        $this->db = $db;
    }
    /**
     * Récupère la liste des membres.
     *
     * @param int $page
     * @param int $perPage
     * @param string $search
     *
     * @return array
     */
    public function getMembers(
        int $page = 1,
        int $perPage = 25,
        string $search = ''
    ): array {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;
        $this->db->table('TABLE_USERS');
        /*
         * Recherche.
         */
        if ($search !== '') {
            $this->db->whereLike(
                'username',
                $search
            );
        }
        /*
         * Tri.
         */
        $this->db->orderby(
            'id',
            'DESC'
        );
        /*
         * Pagination.
         */
        $this->db->limit(
            [
                $offset,
                $perPage
            ],
            true
        );
        $this->db->queryAll();
        if (!is_array($this->db->data)) {
            return [];
        }
        return $this->db->data;
    }
    /**
     * Compte le nombre total de membres.
     *
     * @param string $search
     *
     * @return int
     */
    public function countMembers(
        string $search = ''
    ): int {
        $this->db->table('TABLE_USERS');
        /*
         * Recherche.
         */
        if ($search !== '') {
            $this->db->whereLike(
                'username',
                $search
            );
        }
        return (int)$this->db->count();
    }
    /**
     * Récupère un membre par son hash_key.
     */
    public function getMemberByHashKey(
        string $hashKey
    ): ?object {
        $hashKey = trim($hashKey);
        if ($hashKey === '') {
            return null;
        }
        $this->db->table('TABLE_USERS');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
            'op'    => '=',
        ]);
        $this->db->queryOne();
        if (!is_object($this->db->data)) {
            return null;
        }
        return $this->db->data;
    }
    /**
     * Récupère le profil d'un membre.
     */
    public function getProfileByHashKey(
        string $hashKey
    ): ?object {
        $hashKey = trim($hashKey);
        if ($hashKey === '') {
            return null;
        }
        $this->db->table('TABLE_USERS_PROFILS');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
            'op'    => '=',
        ]);
        $this->db->queryOne();
        if (!is_object($this->db->data)) {
            return null;
        }
        return $this->db->data;
    }
    /**
     * Récupère les réseaux sociaux d'un membre.
     */
    public function getSocialByHashKey(
        string $hashKey
    ): ?object {
        $hashKey = trim($hashKey);
        if ($hashKey === '') {
            return null;
        }
        $this->db->table('TABLE_USERS_SOCIAL');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
            'op'    => '=',
        ]);
        $this->db->queryOne();
        if (!is_object($this->db->data)) {
            return null;
        }
        return $this->db->data;
    }
    /**
     * Récupère les groupes d'un membre.
     */
    public function getGroupsByHashKey(
        string $hashKey
    ): ?object {
        $hashKey = trim($hashKey);
        if ($hashKey === '') {
            return null;
        }
        $this->db->table('TABLE_USERS_GROUPS');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
            'op'    => '=',
        ]);
        $this->db->queryOne();
        if (!is_object($this->db->data)) {
            return null;
        }
        return $this->db->data;
    }
}