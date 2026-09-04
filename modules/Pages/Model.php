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

namespace BelCMS\Modules\Pages;
use BelCMS\Core\BDD;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

final class Model
{
	public function getPages(): array
	{
		$bdd = new BDD();
		$bdd->table('TABLE_PAGES');
        $bdd->fields(['name','publish_date','id_page','description','author','access']);
        $bdd->orderby([
            ['name' => 'name', 'type' => 'DESC']
        ]);
        $bdd->limit(10);
        $bdd->queryAll();
        return $bdd->data;
	}
}
