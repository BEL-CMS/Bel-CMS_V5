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

namespace BelCMS\Core;

if (!defined('CHECK_INDEX')):
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
    exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin:20px auto;text-align:center;color:red;}p{text-align:center;font-weight:bold;}</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on the server.</p></body></html>');
endif;

final class Groups
{
    public function __construct()
    {
        $_SESSION['GROUPS'] = self::getGroups();
    }

    private function getGroups()
    {
        $groups = array();

        $sql = new BDD;
        $sql->table('TABLE_GROUPS');
        $sql->queryAll();
        $return = $sql->data;
        
        foreach ($return as $key => $value) {
            $groups[$value->id_group]['name']  = defined(strtoupper($value->name)) ? constant(strtoupper($value->name)) : ucfirst($value->name);
            $groups[$value->id_group]['color'] = $value->color;
        }

        return $groups;

    }
}