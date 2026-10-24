<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */
declare(strict_types=1);

namespace BelCMS\Admin\Configuration;

use BelCMS\Core\BDD;

final class Model
{
    public function __construct(
        private BDD $db
    ) {
    }

    public function getConfig(): array
    {
        $this->db->table('TABLE_CONFIG');
        $this->db->fields([
            'name',
            'value',
        ]);
        $this->db->queryAll();

        $config = [];

        foreach ($this->db->data as $row) {
            $config[$row->name] = $row->value;
        }

        return $config;
    }

    public function save (array $data) : bool
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $this->db->table('TABLE_CONFIG');
                $this->db->where(array('name' => 'name', 'value' => strtoupper($k)));
                $this->db->update(array('value' => $v));
            }
        } else {
            return false;
        }
        return true;
    }
}