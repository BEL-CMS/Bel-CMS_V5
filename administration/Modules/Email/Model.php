<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */

declare(strict_types=1);

namespace BelCMS\Admin\Email;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

use BelCMS\Core\BDD;
use InvalidArgumentException;

final class Model
{
    public function __construct(private BDD $db) {}

	public function getConfig () : array
	{
		$this->db->table('TABLE_CONFIG_MAIL');
		$this->db->queryAll();
		return $this->db->data;
	}

	public function save (array $data) : bool
	{
		$return = false;
		foreach ($data as $k => $v) {
			$sql = new BDD;
			$sql->table('TABLE_CONFIG_MAIL');
			$sql->where(array('name' => 'name', 'value' => $k));
			$sql->update(array('value' => $v));
			$return = true;
		}
		return $return;
	}
}
