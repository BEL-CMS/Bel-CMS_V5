<?php

declare(strict_types=1);

namespace BelCMS\Admin\Dashboard;

use BelCMS\Core\BDD;

final class Model
{
    public function __construct(
        private BDD $db
    ) {
    }

    /**
     * Nombre total de membres.
     */
    public function countUsers(): int
    {
        $this->db->table(TABLE_USERS);
        $this->db->count();

        return (int) $this->db->data;
    }

    /**
     * Nombre de profils.
     */
    public function countProfiles(): int
    {
        $this->db->table(TABLE_USERS_PROFILS);
        $this->db->count();

        return (int) $this->db->data;
    }

    /**
     * Nombre de groupes.
     */
    public function countGroups(): int
    {
        $this->db->table(TABLE_GROUPS);
        $this->db->count();

        return (int) $this->db->data;
    }

    /**
     * Nombre de sessions actives.
     */
    public function countSessions(): int
    {
        $this->db->table(TABLE_SESSION);
        $this->db->count();

        return (int) $this->db->data;
    }

    /**
     * Récupère les statistiques du dashboard.
     */
    public function getStatistics(): array
    {
        return [
            'users'     => $this->countUsers(),
            'profiles'  => $this->countProfiles(),
            'groups'    => $this->countGroups(),
            'sessions'  => $this->countSessions(),
        ];
    }
}