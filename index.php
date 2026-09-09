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
#######################################################
# Demarre une $_SESSION                               #
#######################################################
if (session_status() === PHP_SESSION_NONE) {          #
	session_start([                                   #
		'cookie_httponly' => true,                    #
		'cookie_secure'   => true,                    #
		'cookie_samesite' => 'Strict'                 #
	]);                                               #
}                                                     #
#######################################################
# TimeZone et charset                                 #
#######################################################
ini_set('default_charset', 'utf-8');                  #
date_default_timezone_set('Europe/Brussels');         #
#######################################################
# Install                                             #
#######################################################
if (is_file(__DIR__.'/INSTALL/index.php')) {          #
	header('Location: /INSTALL/index.php');           #
	die();                                            #
}                                                     #
#######################################################
define('CHECK_INDEX', true);                          #
#######################################################
require_once __DIR__ . '/system/helpers.php';         #
require_once __DIR__ . '/system/common.php';          #
require_once __DIR__ . '/system/Autoloader.php';      #
#######################################################
#                Autoloader                           #
#######################################################
BelCMS\Autoloader::register();                        #
#######################################################
$app = new BelCMS\Core\Application();                 #
$GLOBALS['belcms_app'] = $app;                        #
$app->run();                                          #
#######################################################