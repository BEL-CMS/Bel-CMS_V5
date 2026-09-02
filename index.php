<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @link https://determe.be
 * @license MIT License
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/
#######################################################
# Demarre une $_SESSION                               #
#######################################################
if (session_status() === PHP_SESSION_NONE) {          #
	session_start([                                   #
		'cookie_httponly' => true,                    #
		'cookie_secure'   => true,                    #
		'cookie_samesite' => 'Strict'                 #
	]);                                               #
}
########################################################
$_SESSION['belcms_captcha_attempts'] ??= 0;           #
$_SESSION['belcms_captcha_blocked_until'] ??= 0;      #
#######################################################
# TimeZone et charset                                 #
#######################################################
ini_set('default_charset', 'utf-8');                  #
date_default_timezone_set('Europe/Brussels');         #
#######################################################
# Définit comme l'index                               #
#######################################################
define('CHECK_INDEX', true);                          #
define('VERSION_CMS', '5.0.0');                       #
define('DS', DIRECTORY_SEPARATOR);                    #
define('ROOT', __DIR__);                              #
define('ROOT_DOC', $_SERVER['DOCUMENT_ROOT']);        #
define('SHOW_ALL_REQUEST_SQL', false);                #
#######################################################
# Function debug                                      #
#######################################################
require_once 'debug.php';                             #
#######################################################
# MicroTime loading                                   #
#######################################################
$_SESSION['SESSION_START']  = microtime(true);        #
#########################################             #
$_SESSION['NB_REQUEST_SQL'] = 0;                      #
$_SESSION['CMS_DEBUG']      = true;                   #
#######################################################
# Install                                             #
#######################################################
if (is_file(ROOT.DS.'INSTALL'.DS.'index.php')) {      #
	header('Location: INSTALL/index.php');            #
	die();                                            #
}                                                     #
#######################################################