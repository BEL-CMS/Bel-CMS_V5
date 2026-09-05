<?php
/**
 * Bel-CMS [Content management system]
 * @version 4.2.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @link https://determe.be
 * @license MIT License
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/


declare(strict_types=1);

spl_autoload_register(static function (string $class): void {

    $prefix = 'BelCMS\\Core\\Security\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));

    $file = __DIR__ . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});