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

namespace BelCMS;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([
            self::class,
            'load'
        ]);
    }

    private static function load(string $class): void
    {
        $prefix = 'BelCMS\\';

        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $class = substr($class, strlen($prefix));

        /*
         * Classes Core
         */
        if (str_starts_with($class, 'Core\\')) {

            $class = substr(
                $class,
                strlen('Core\\')
            );

            $file = dirname(__DIR__)
                . DIRECTORY_SEPARATOR
                . 'system'
                . DIRECTORY_SEPARATOR
                . 'Core'
                . DIRECTORY_SEPARATOR
                . str_replace(
                    '\\',
                    DIRECTORY_SEPARATOR,
                    $class
                )
                . '.php';

            if (is_file($file)) {
                require_once $file;
            }

            return;
        }

        /*
         * Classes Modules
         */
        if (str_starts_with($class, 'Modules\\')) {

            $class = substr(
                $class,
                strlen('Modules\\')
            );

            $file = dirname(__DIR__)
                . DIRECTORY_SEPARATOR
                . 'modules'
                . DIRECTORY_SEPARATOR
                . str_replace(
                    '\\',
                    DIRECTORY_SEPARATOR,
                    $class
                )
                . '.php';

            if (is_file($file)) {
                require_once $file;
            }

            return;
        }
        /*
        * Classes Administration
        */
        if (str_starts_with($class, 'Admin\\')) {

            $class = substr(
                $class,
                strlen('Admin\\')
            );

            $file = dirname(__DIR__)
                . DIRECTORY_SEPARATOR
                . 'administration'
                . DIRECTORY_SEPARATOR
                . 'Modules'
                . DIRECTORY_SEPARATOR
                . str_replace(
                    '\\',
                    DIRECTORY_SEPARATOR,
                    $class
                )
                . '.php';

            if (is_file($file)) {
                require_once $file;
            }

            return;
        }
    }
}