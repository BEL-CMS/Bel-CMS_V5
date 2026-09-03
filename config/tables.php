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
$prefix = $config['prefix'] ?? '';

$tables = [
    'TABLE_NEWS'              => $prefix . 'news',
    'TABLE_NEWS_CAT'          => $prefix . 'news_cat',
    'TABLE_PAGES'             => $prefix . 'pages',
    'TABLE_PAGES_CONTENT'     => $prefix . 'pages_content',
];

foreach ($tables as $name => $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

return $tables;
