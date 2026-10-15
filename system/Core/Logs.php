<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/

declare(strict_types=1);

namespace BelCMS\Core;

use BelCMS\Core\User;
use BelCMS\system\Common;
use Exception;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

/* UTILISATION */
/*
	$new = new Logs;
	$new->user(constant('HASH_KEY_USER'));
	$new->message('teste de message');
	$new->type(0); 0 pour admin & 1 pour les modules
	$new->module('NomDuModule');
	$new->save();
*/

final class Logs
{
	public string $user;
	public string $ip;
	public string $message;
	public string $module;
	public string $type;
	private BDD $db;

	public function __construct()
	{
		$this->ip = self::getIp();
	}

	public function user (string $user)
	{
		if (strlen($user) == 32) {
			if (User::isUserExist($user)) {
				$this->user = $user;
			} else {
				$this->user = Common::GetIp();
			}
		} else {
			$this->user = Common::GetIp();
		}
	}

	public function getIp () : string
	{
		return Common::GetIp();
	}

	public function message (string $message)
	{
		$this->message = htmlspecialchars(trim($message), ENT_QUOTES, 'UTF-8');
	}

	public function type (int $id)
	{
		switch ($id) {
			case '0':
				$type = 'SiteWeb';
			break;

			case '1':
				$type = 'Administration';
			break;

			default:
				$type = 'Inconnu';
			break;
		};
		$this->type = $type;
	}

	public function module (string $module)
	{
		$this->module = htmlspecialchars(trim($module), ENT_QUOTES, 'UTF-8');
	}

	public function save ()
	{
		$data['user']    = $this->user;
		$data['ip']      = $this->ip;
		$data['message'] = $this->message;
		$data['type']    = $this->type;
		$data['module']  = $this->module;

		try {
			$this->db = new BDD();
			$this->db->table('TABLE_LOGS');
			$this->db->insert($data);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
	}

	public function getAllLogs () : array
	{
		$this->db = new BDD();
		$this->db->table('TABLE_LOGS');
        $this->db->orderby([['name' => 'id','type' => 'DESC',]]);
		$this->db->queryLarge();
		return $this->db->data;
	}

	public function getLogoUser (string $user) : array
	{
		$this->db = new BDD();
		$this->db->table('TABLE_LOGS');
		$this->db->where(array('name' => 'user', 'value' => $user));
		$this->db->orderby([['name' => 'id','type' => 'DESC',]]);
		$this->db->queryLarge();
		return $this->db->data;
	}
}