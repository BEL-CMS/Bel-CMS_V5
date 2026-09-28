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

use RuntimeException;

final class AdminRoutes
{
    private Router $router;
    private string $administrationPath;
    public function __construct(
        Router $router,
        ?string $administrationPath = null
    ) {
        $this->router = $router;
        $this->administrationPath =
            $administrationPath
            ?? dirname(__DIR__, 2)
                . '/administration';
    }
    /**
     * Charge les routes globales
     * de l'administration.
     */
    public function load(): void
    {
        /*
         * Routes générales
         */
        $this->loadFile(
            $this->administrationPath
            . '/routes.php',
            null
        );
        /*
         * Routes des modules
         */
        $modulesPath =
            $this->administrationPath
            . '/Modules';
        if (!is_dir($modulesPath)) {
            return;
        }
        $directories = scandir(
            $modulesPath
        );
        if ($directories === false) {
            return;
        }
        foreach ($directories as $directory) {
            if (
                $directory === '.'
                || $directory === '..'
            ) {
                continue;
            }
            $modulePath =
                $modulesPath
                . DIRECTORY_SEPARATOR
                . $directory;
            if (!is_dir($modulePath)) {
                continue;
            }
            $routesFile =
                $modulePath
                . DIRECTORY_SEPARATOR
                . 'routes.php';
            if (!is_file($routesFile)) {
                continue;
            }
            $this->loadFile(
                $routesFile,
                $directory
            );
        }
    }
    /**
     * Charge un fichier routes.php.
     */
    private function loadFile(
        string $file,
        ?string $module
    ): void {
        if (!is_file($file)) {
            return;
        }
        $routes = require $file;
        if (!is_array($routes)) {
            throw new RuntimeException(
                'Le fichier de routes '
                . $file
                . ' doit retourner un tableau.'
            );
        }
        foreach ($routes as $route) {
            if (!is_array($route)) {
                continue;
            }
            $method = strtoupper(
                (string) (
                    $route['method']
                    ?? 'GET'
                )
            );
            $path = trim(
                (string) (
                    $route['path']
                    ?? ''
                )
            );
            $handler =
                $route['handler']
                ?? null;
            if (
                $path === ''
                || $handler === null
            ) {
                continue;
            }
            /*
             * Route GET
             */
            if ($method === 'GET') {
                $this->router->get(
                    $path,
                    $handler,
                    $module
                );
                continue;
            }
            /*
             * Route POST
             */
            if ($method === 'POST') {
                $this->router->post(
                    $path,
                    $handler,
                    $module
                );
                continue;
            }
            throw new RuntimeException(
                'Méthode HTTP non supportée : '
                . $method
                . ' dans '
                . $file
            );
        }
    }
}