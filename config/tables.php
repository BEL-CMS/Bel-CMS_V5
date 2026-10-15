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

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

$config = require __DIR__ . '/database.php';
$DB_PREFIX = $config['prefix'] ?? '';

$tables = [
    'TABLE_BANISHMENT'        => $DB_PREFIX . 'banishment',
    'TABLE_BANISHMENT_LOGS'   => $DB_PREFIX . 'banishment_logs',
    'TABLE_CAPTCHA'           => $DB_PREFIX . 'captcha',
    'TABLE_BELCMS_SHIELD'     => $DB_PREFIX . 'captcha_shield',
    'TABLE_CONFIG'            => $DB_PREFIX . 'config',
    'TABLE_CAPTCHA_LOGS'      => $DB_PREFIX . 'captcha_log',
    'TABLE_CAPTCHA_BLACKLIST' => $DB_PREFIX . 'captcha_blacklist',
    'TABLE_GROUPS'            => $DB_PREFIX . 'groups', 
    'TABLE_LOGS'              => $DB_PREFIX . 'logs', 
    'TABLE_NEWS'              => $DB_PREFIX . 'news',
    'TABLE_NEWS_CAT'          => $DB_PREFIX . 'news_cat',
    'TABLE_PAGES'             => $DB_PREFIX . 'pages',
    'TABLE_PAGES_CONTENT'     => $DB_PREFIX . 'pages_content',
    'TABLE_SESSION'           => $DB_PREFIX . 'user_sessions',
    'TABLE_USERS'             => $DB_PREFIX . 'users',
    'TABLE_USERS_GROUPS'      => $DB_PREFIX . 'users_groups',
    'TABLE_USERS_PROFILS'     => $DB_PREFIX . 'users_profils',
    'TABLE_USERS_SOCIAL'      => $DB_PREFIX . 'users_social',
    'TABLE_RECOVERY'          => $DB_PREFIX . 'user_recovery',
    'TABLE_USERS_SESSIONS'    => $DB_PREFIX . 'user_sessions',
    'TABLE_USERS_TMP'         => $DB_PREFIX . 'user_temp',
];

foreach ($tables as $name => $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

return $tables;
