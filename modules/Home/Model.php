<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @link https://determe.be
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
 */
declare(strict_types=1);
namespace BelCMS\Modules\Home;
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
     * Nombre total de profils.
     */
    public function countProfiles(): int
    {
        $this->db->table(TABLE_USERS_PROFILS);
        $this->db->count();
        return (int) $this->db->data;
    }
    /**
     * Nombre total de groupes.
     */
    public function countGroups(): int
    {
        $this->db->table(TABLE_GROUPS);
        $this->db->count();
        return (int) $this->db->data;
    }
    /**
     * Nombre total de news publiées.
     */
    public function countNews(): int
    {
        $this->db->table(TABLE_NEWS);
        $this->db->where([
            [
                'name'  => 'status',
                'value' => 1,
            ],
        ]);
        $this->db->count();
        return (int) $this->db->data;
    }
    /**
     * Récupère toutes les statistiques de la Home.
     */
    public function getStatistics(): array
    {
        return [
            'users'    => $this->countUsers(),
            'profiles' => $this->countProfiles(),
            'groups'   => $this->countGroups(),
            'news'     => $this->countNews(),
        ];
    }
}