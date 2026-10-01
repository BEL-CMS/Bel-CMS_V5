<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */
declare(strict_types=1);

namespace BelCMS\Admin\Groups;

use BelCMS\Core\BDD;

final class Model
{
    public function __construct(
        private BDD $db
    ) {
    }
    /**
     * Récupère tous les groupes.
     *
     * Le nombre de membres est récupéré séparément afin
     * de rester compatible avec le BDD actuel.
     */
    public function getGroups(): array
    {
        $this->db->table('TABLE_GROUPS');
        $this->db->fields([
            'id',
            'name',
            'id_group',
            'image',
            'color',
            'description',
        ]);
        $this->db->orderby([
            [
                'name' => 'id',
                'type' => 'ASC',
            ],
        ]);
        $groups = $this->db->queryAll();
        /*
         * Ajout du nombre de membres.
         */
        foreach ($groups as $group) {
            $group->member_count = $this->countGroupMembers(
                (int) $group->id_group
            );
        }
        return $groups;
    }
    /**
     * Récupère un groupe grâce à son id_group.
     */
    public function getGroupByIdGroup(int $idGroup): ?object
    {
        if ($idGroup <= 0) {
            return null;
        }
        $this->db->table('TABLE_GROUPS');
        $this->db->fields([
            'id',
            'name',
            'id_group',
            'image',
            'color',
            'description',
        ]);
        $this->db->where([
            [
                'name'  => 'id_group',
                'value' => $idGroup,
            ],
        ]);
        $this->db->queryOne();
        return is_object($this->db->data)
            ? $this->db->data
            : null;
    }
    /**
     * Vérifie si un nom de groupe existe déjà.
     *
     * $excludeIdGroup permet d'ignorer le groupe
     * actuellement modifié.
     */
    public function groupNameExists(
        string $name,
        ?int $excludeIdGroup = null
    ): bool {
        $name = trim($name);
        if ($name === '') {
            return false;
        }
        $this->db->table('TABLE_GROUPS');
        $this->db->fields([
            'id_group',
        ]);
        $conditions = [
            [
                'name'  => 'name',
                'value' => $name,
            ],
        ];
        if ($excludeIdGroup !== null && $excludeIdGroup > 0) {
            $conditions[] = [
                'name'  => 'id_group',
                'value' => $excludeIdGroup,
                'op'    => '!=',
            ];
        }
        $this->db->where($conditions);
        $result = $this->db->queryOne();
        return $result !== false;
    }
    /**
     * Retourne le prochain id_group disponible.
     *
     * Les groupes 1 et 2 sont réservés aux groupes système.
     */
    public function getNextGroupId(): int
    {
        $this->db->table('TABLE_GROUPS');
        $this->db->fields([
            'id_group',
        ]);
        $this->db->orderby([
            [
                'name' => 'id_group',
                'type' => 'DESC',
            ],
        ]);
        $this->db->limit(1);
        $group = $this->db->queryOne();
        if (!$group) {
            return 3;
        }
        $next = ((int) $group->id_group) + 1;
        return max(3, $next);
    }
    /**
     * Crée un nouveau groupe.
     */
    public function createGroup(array $data): bool
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            return false;
        }
        $idGroup = (int) ($data['id_group'] ?? 0);
        if ($idGroup < 3) {
            return false;
        }
        $image = trim(
            (string) ($data['image'] ?? '')
        );
        $color = trim(
            (string) ($data['color'] ?? '#000000')
        );
        $description = trim(
            (string) ($data['description'] ?? '')
        );
        $this->db->table('TABLE_GROUPS');
        return $this->db->insert([
            'name' => $name,
            'id_group' => $idGroup,
            'image' => $image,
            'color' => $color,
            'description' => $description,
        ]);
    }
    /**
     * Modifie un groupe.
     *
     * id_group n'est volontairement jamais modifié.
     */
    public function updateGroup(
        int $idGroup,
        array $data
    ): bool {
        if ($idGroup <= 0) {
            return false;
        }
        $name = trim(
            (string) ($data['name'] ?? '')
        );
        if ($name === '') {
            return false;
        }
        $image = trim(
            (string) ($data['image'] ?? '')
        );
        $color = trim(
            (string) ($data['color'] ?? '#000000')
        );
        $description = trim(
            (string) ($data['description'] ?? '')
        );
        $this->db->table('TABLE_GROUPS');
        $this->db->where([
            [
                'name' => 'id_group',
                'value' => $idGroup,
            ],
        ]);
        return $this->db->update([
            'name' => $name,
            'image' => $image,
            'color' => $color,
            'description' => $description,
        ]);
    }
    /**
     * Compte les membres d'un groupe.
     */
    public function countGroupMembers(int $idGroup): int
    {
        if ($idGroup <= 0) {
            return 0;
        }
        $this->db->table('TABLE_USERS_GROUPS');
        $this->db->fields([
            'COUNT(*) AS total',
        ]);
        $this->db->where([
            [
                'name' => 'user_group',
                'value' => $idGroup,
            ],
        ]);
        $result = $this->db->queryOne();
        return (int) ($result->total ?? 0);
    }
    /**
     * Récupère les membres d'un groupe.
     */
    public function getGroupMembers(int $idGroup): array
    {
        if ($idGroup <= 0) {
            return [];
        }
        $this->db->table('TABLE_USERS_GROUPS');
        $this->db->fields([
            TABLE_USERS_GROUPS . '.hash_key',
            TABLE_USERS_GROUPS . '.user_group',
            TABLE_USERS . '.id',
            TABLE_USERS . '.username',
            TABLE_USERS . '.email',
            TABLE_USERS . '.valid',
        ]);
        $this->db->join([
            [
                'table' => 'TABLE_USERS',
                'type'  => 'INNER',
                'on'    => TABLE_USERS_GROUPS . '.hash_key = ' . TABLE_USERS . '.hash_key',
            ],
        ]);
        $this->db->where([
            [
                'name'  => TABLE_USERS_GROUPS . '.user_group',
                'value' => $idGroup,
            ],
        ]);
        $this->db->orderby(TABLE_USERS . '.username', true);
        $this->db->queryAll();
        return is_array($this->db->data)
            ? $this->db->data
            : [];
    }
    /**
     * Supprime un groupe.
     *
     * Les groupes système 1 et 2 sont protégés.
     */
    public function deleteGroup(int $idGroup): bool
    {
        if ($idGroup <= 0) {
            return false;
        }
        /*
         * Protection des groupes système.
         */
        if (in_array($idGroup, [1, 2], true)) {
            return false;
        }
        /*
         * Un groupe contenant des membres
         * ne peut pas être supprimé.
         */
        if ($this->countGroupMembers($idGroup) > 0) {
            return false;
        }
        $this->db->table('TABLE_GROUPS');
        $this->db->where([
            [
                'name' => 'id_group',
                'value' => $idGroup,
            ],
        ]);
        return $this->db->delete();
    }
    /**
     * Recherche les utilisateurs qui ne sont pas encore
     * membres du groupe.
     */
    public function searchUsersForGroup(
        int $idGroup,
        string $search = ''
    ): array {
        if ($idGroup <= 0) {
            return [];
        }
        $search = trim($search);
        $this->db->table('TABLE_USERS');
        $this->db->fields([
            TABLE_USERS . '.id',
            TABLE_USERS . '.hash_key',
            TABLE_USERS . '.username',
            TABLE_USERS . '.email',
            TABLE_USERS . '.valid',
        ]);
        /*
        * On récupère uniquement les utilisateurs
        * qui ne sont pas déjà dans ce groupe.
        */
        $this->db->join([
            [
                'table' => 'TABLE_USERS_GROUPS',
                'type'  => 'LEFT',
                'on'    => TABLE_USERS . '.hash_key = '
                        . TABLE_USERS_GROUPS . '.hash_key'
                        . ' AND '
                        . TABLE_USERS_GROUPS . '.user_group = '
                        . (int) $idGroup,
            ],
        ]);
        /*
        * Exclure les membres déjà présents.
        */
        $this->db->where([
            [
                'name'  => TABLE_USERS_GROUPS . '.hash_key',
                'value' => null,
                'op'    => 'IS NULL',
            ],
        ]);
        /*
        * Recherche par nom d'utilisateur ou adresse e-mail.
        */
        if ($search !== '') {
            $this->db->whereOr([
                [
                    'name'  => TABLE_USERS . '.username',
                    'value' => '%' . $search . '%',
                    'op'    => 'LIKE',
                ],
                [
                    'name'  => TABLE_USERS . '.email',
                    'value' => '%' . $search . '%',
                    'op'    => 'LIKE',
                ],
            ]);
        }
        $this->db->orderby(
            TABLE_USERS . '.username ASC',
            true
        );
        $this->db->limit(50);
        $this->db->queryAll();
        return is_array($this->db->data)
            ? $this->db->data
            : [];
    }
    /**
     * Vérifie qu'un utilisateur existe à partir de son hash_key.
     */
    public function userExistsByHashKey(string $hashKey): bool
    {
        $hashKey = trim($hashKey);
        if ($hashKey === '') {
            return false;
        }
        $this->db->table('TABLE_USERS');
        $this->db->fields([
            'id',
            'hash_key',
        ]);
        $this->db->where([
            [
                'name'  => 'hash_key',
                'value' => $hashKey,
            ],
        ]);
        $this->db->queryOne();
        return is_object($this->db->data);
    }
    /**
     * Ajoute un utilisateur à un groupe.
     */
    public function addMemberToGroup(
        string $hashKey,
        int $idGroup
    ): bool {
        $hashKey = trim($hashKey);
        if ($hashKey === '' || $idGroup <= 0) {
            return false;
        }
        /*
        * Vérifie que l'utilisateur existe.
        */
        if (!$this->userExistsByHashKey($hashKey)) {
            return false;
        }
        /*
        * Vérifie que le groupe existe.
        */
        $group = $this->getGroupByIdGroup($idGroup);
        if ($group === null) {
            return false;
        }
        /*
        * Vérifie que l'utilisateur n'est pas
        * déjà membre du groupe.
        */
        $this->db->table('TABLE_USERS_GROUPS');
        $this->db->where([
            [
                'name'  => 'hash_key',
                'value' => $hashKey,
            ],
            [
                'name'  => 'user_group',
                'value' => $idGroup,
            ],
        ]);
        $this->db->queryOne();
        if (is_object($this->db->data)) {
            return false;
        }
        /*
        * Ajout du membre.
        */
        $this->db->table('TABLE_USERS_GROUPS');
        return $this->db->insert([
            'hash_key'    => $hashKey,
            'user_group'  => $idGroup,
            'user_groups' => '',
        ]);
    }
    /**
     * Retire un utilisateur d'un groupe.
     */
    public function removeMemberFromGroup(
        string $hashKey,
        int $idGroup
    ): bool {
        $hashKey = trim($hashKey);
        if ($hashKey === '' || $idGroup <= 0) {
            return false;
        }
        // Vérifie que l'utilisateur existe
        if (!$this->userExistsByHashKey($hashKey)) {
            return false;
        }
        // Vérifie que le groupe existe
        $group = $this->getGroupByIdGroup($idGroup);
        if ($group === null) {
            return false;
        }
        // Suppression de l'association
        $this->db->table('TABLE_USERS_GROUPS');
        $this->db->where([
            [
                'name'  => 'hash_key',
                'value' => $hashKey,
            ],
            [
                'name'  => 'user_group',
                'value' => $idGroup,
            ],
        ]);
        return $this->db->delete();
    }
}