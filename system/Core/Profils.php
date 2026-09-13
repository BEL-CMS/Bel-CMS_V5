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
use BelCMS\Core\BDD;

final class Profils
{
    public function __construct()
    {
        $_SESSION['USER'] = (object) array();
        if (isset($_SESSION['BELCMS_USER_HASH_KEY']) and strlen($_SESSION['BELCMS_USER_HASH_KEY']) == 32) {
            $_SESSION['USER']->profils = self::getProfils($_SESSION['BELCMS_USER_HASH_KEY']);
        }
    }

    private function getProfils (string $hash_key)
    {
        $sql = new BDD;
        $sql->table('TABLE_USER_PROFILS');
        $sql->where(array('name' => 'hash_key', 'value' => $hash_key));
        $sql->queryOne();
        $return = $sql->data;
        return $return;
    }
}