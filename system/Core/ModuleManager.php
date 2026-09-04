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
namespace BelCMS\Core;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

use RuntimeException;

final class ModuleManager
{
    private string $modulesPath;

    private Router $router;

    private Assets $assets;

    private Language $language;

    private array $modules = [];

    public function __construct(Router $router, Assets $assets, Language $language, ?string $modulesPath = null) 
    {
        $this->router = $router;
        $this->assets = $assets;
        $this->language = $language;
        $this->modulesPath = $modulesPath
            ?? dirname(__DIR__, 2) . '/modules';
    }
    /**
     * Découvre les modules disponibles
     */
    public function discover(): array
    {
        $this->modules = [];

        if (!is_dir($this->modulesPath)) {
            return [];
        }

        $directories = scandir($this->modulesPath);

        if ($directories === false) {
            return [];
        }

        foreach ($directories as $directory) {

            if (
                $directory === '.'
                || $directory === '..'
            ) {
                continue;
            }

            $path = $this->modulesPath
                . DIRECTORY_SEPARATOR
                . $directory;

            if (!is_dir($path)) {
                continue;
            }

            $this->modules[$directory] = [
                'name' => $directory,
                'path' => $path,
            ];
        }

        return $this->modules;
    }
    /**
     * Charge un module
     */
    public function load(string $name): bool
    {
        $path = $this->modulesPath
            . DIRECTORY_SEPARATOR
            . $name;

        if (!is_dir($path)) {
            throw new RuntimeException(
                "Le module {$name} n'existe pas."
            );
        }
        /*
         * -------------------------------------------------
         * Configuration du module
         * -------------------------------------------------
         */
        $moduleFile = $path
            . DIRECTORY_SEPARATOR
            . 'module.php';
        $moduleConfig = [];
        if (is_file($moduleFile)) {
            $moduleConfig = require $moduleFile;
            if (!is_array($moduleConfig)) {
                throw new RuntimeException(
                    "Le fichier module.php du module {$name} "
                    . "doit retourner un tableau."
                );
            }
        }
        /*
         * -------------------------------------------------
         * Langue du module
         * -------------------------------------------------
         */
        $this->language->loadModule($name);
        /*
         * -------------------------------------------------
         * Assets du module
         * -------------------------------------------------
         */
        if (
            isset($moduleConfig['assets'])
            && is_array($moduleConfig['assets'])
        ) {
            /*
             * CSS
             */
            if (
                isset($moduleConfig['assets']['css'])
                && $moduleConfig['assets']['css'] === true
            ) {
                $this->assets->moduleCss($name);
            }
            /*
             * JavaScript
             */
            if (
                isset($moduleConfig['assets']['js'])
                && $moduleConfig['assets']['js'] === true
            ) {
                $this->assets->moduleJs($name);
            }
        }
        /*
         * -------------------------------------------------
         * Routes du module
         * -------------------------------------------------
         */
        $routesFile = $path
            . DIRECTORY_SEPARATOR
            . 'routes.php';
        if (is_file($routesFile)) {

            $routes = require $routesFile;

            if (!is_array($routes)) {
                throw new RuntimeException(
                    "Le fichier routes.php du module {$name} "
                    . "doit retourner un tableau."
                );
            }

            $this->registerRoutes($routes);
        }
        return true;
    }

    /**
     * Enregistre les routes du module
     */
    private function registerRoutes(
        array $routes
    ): void {
        foreach ($routes as $route => $handler) {

            if (!is_string($route)) {
                continue;
            }

            [$method, $path] = $this->parseRoute(
                $route
            );
            switch ($method) {

                case 'GET':

                    $this->router->get(
                        $path,
                        $handler
                    );

                    break;

                case 'POST':

                    $this->router->post(
                        $path,
                        $handler
                    );

                    break;

                default:

                    throw new RuntimeException(
                        "Méthode HTTP non supportée : {$method}"
                    );
            }
        }
    }
    /**
     * Analyse une route
     *
     * Exemple :
     *
     * GET /news
     * POST /news
     */
    private function parseRoute(
        string $route
    ): array {
        $route = trim($route);

        $parts = preg_split(
            '/\s+/',
            $route,
            2
        );

        if (
            $parts === false
            || count($parts) !== 2
        ) {
            throw new RuntimeException(
                "Route invalide : {$route}"
            );
        }

        return [
            strtoupper($parts[0]),
            $parts[1]
        ];
    }
    /**
     * Charge tous les modules
     */
    public function loadAll(): void
    {
        $modules = $this->discover();

        foreach ($modules as $module) {

            $this->load(
                $module['name']
            );
        }
    }
    /**
     * Vérifie si un module existe
     */
    public function exists(
        string $name
    ): bool {
        return is_dir(
            $this->modulesPath
            . DIRECTORY_SEPARATOR
            . $name
        );
    }
    /**
     * Retourne tous les modules
     */
    public function all(): array
    {
        return $this->modules;
    }
    /**
     * Retourne le chemin des modules
     */
    public function path(): string
    {
        return $this->modulesPath;
    }
}