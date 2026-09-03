<?php

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
