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

namespace BelCMS;

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
    }
}
