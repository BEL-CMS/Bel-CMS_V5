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

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

final class AdminMenu
{
    /**
     * Chemin des modules d'administration.
     */
    private string $modulesPath;

    /**
     * ModuleManager principal.
     */
    private ModuleManager $modules;

    /**
     * Configuration des modules.
     */
    private array $items = [];

    /**
     * Module actuellement sélectionné.
     */
    private ?array $currentModule = null;

    /**
     * URL actuellement utilisée.
     */
    private string $currentPath = '';

    public function __construct(
        ModuleManager $modules,
        ?string $modulesPath = null
    ) {
        $this->modules = $modules;

        $this->modulesPath = $modulesPath
            ?? dirname(__DIR__, 2)
            . '/administration/Modules';

        $this->load();
    }

    /**
     * Charge les menus des modules.
     */
    private function load(): void
    {
        $this->items = [];

        if (!is_dir($this->modulesPath)) {
            return;
        }

        $directories = scandir(
            $this->modulesPath
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

            $path = $this->modulesPath
                . DIRECTORY_SEPARATOR
                . $directory;

            if (!is_dir($path)) {
                continue;
            }

            $moduleFile = $path
                . DIRECTORY_SEPARATOR
                . 'module.php';

            /*
             * Le module doit posséder un module.php
             */
            if (!is_file($moduleFile)) {
                continue;
            }

            $config = require $moduleFile;

            if (!is_array($config)) {
                throw new RuntimeException(
                    "Le fichier module.php du module "
                    . $directory
                    . " doit retourner un tableau."
                );
            }

            /*
             * Pas de menu d'administration
             */
            if (
                !isset($config['admin'])
                || !is_array($config['admin'])
            ) {
                continue;
            }

            $admin = $config['admin'];

            /*
             * Informations principales
             */
            $label = trim(
                (string) (
                    $admin['label']
                    ?? $config['name']
                    ?? $directory
                )
            );

            if ($label === '') {
                $label = $directory;
            }

            $icon = trim(
                (string) (
                    $admin['icon']
                    ?? 'fa-puzzle-piece'
                )
            );

            $order = (int) (
                $admin['order']
                ?? 999
            );

            /*
             * Catégorie explicite de la sidebar.
             * Priorité à admin.category, avec compatibilité
             * pour category défini à la racine de module.php.
             */
            $allowedCategories = [
                'Accueil',
                'Paramètres',
                'Pages',
                'Utilisateurs',
                'Widgets',
                'Extras',
                'Teams',
            ];

            $category = trim((string) (
                $admin['category']
                ?? $config['category']
                ?? 'Extras'
            ));

            foreach ($allowedCategories as $allowedCategory) {
                if (strcasecmp($category, $allowedCategory) === 0) {
                    $category = $allowedCategory;
                    break;
                }
            }

            if (!in_array($category, $allowedCategories, true)) {
                $category = 'Extras';
            }

            /*
             * Route principale du module d'administration.
             *
             * Exemple :
             * User -> /admin/users
             */
            $adminRoute = trim(
                (string) (
                    $admin['route']
                    ?? ''
                )
            );

            /*
             * Menu interne du module
             */
            $menu = [];

            if (
                isset($admin['menu'])
                && is_array($admin['menu'])
            ) {
                foreach ($admin['menu'] as $menuItem) {

                    if (!is_array($menuItem)) {
                        continue;
                    }

                    $menuLabel = trim(
                        (string) (
                            $menuItem['label']
                            ?? ''
                        )
                    );

                    $route = trim(
                        (string) (
                            $menuItem['route']
                            ?? ''
                        )
                    );

                    if (
                        $menuLabel === ''
                        || $route === ''
                    ) {
                        continue;
                    }

                    $menuIcon = trim(
                        (string) (
                            $menuItem['icon']
                            ?? ''
                        )
                    );

                    $menuOrder = (int) (
                        $menuItem['order']
                        ?? 999
                    );

                    $menu[] = [
                        'label'  => $menuLabel,
                        'route'  => $route,
                        'icon'   => $menuIcon,
                        'order'  => $menuOrder,
                    ];
                }
            }

            /*
             * Tri du menu interne
             */
            usort(
                $menu,
                static function (
                    array $a,
                    array $b
                ): int {
                    return $a['order']
                        <=> $b['order'];
                }
            );

            /*
             * Enregistrement du module
             */
            $this->items[] = [
                'module'  => $directory,
                'name'    => (string) (
                    $config['name']
                    ?? $directory
                ),
                'version' => (string) (
                    $config['version']
                    ?? ''
                ),
                'author'  => (string) (
                    $config['author']
                    ?? ''
                ),
                'label'    => $label,
                'icon'     => $icon,
                'category' => $category,
                'route'    => $adminRoute,
                'order'   => $order,
                'path'    => $path,
                'menu'    => $menu,
            ];

        }

        /*
         * Tri des modules
         */
        usort(
            $this->items,
            static function (
                array $a,
                array $b
            ): int {
                return $a['order']
                    <=> $b['order'];
            }
        );
    }

    /**
     * Retourne tous les modules
     * disposant d'un menu d'administration.
     */
/**
 * Retourne tous les éléments du menu.
 *
 * Alias conservé pour compatibilité
 * avec le layout de l'administration.
 */
public function getItems(): array
{
    return $this->items;
}

/**
 * Alias de getItems().
 */
public function all(): array
{
    return $this->getItems();
}

    /**
     * Retourne un module par son nom.
     */
    public function get(
        string $module
    ): ?array {
        foreach ($this->items as $item) {

            if (
                strcasecmp(
                    $item['module'],
                    $module
                ) === 0
            ) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Détermine le module correspondant
     * à une URL d'administration.
     */
    public function findByPath(
        string $path
    ): ?array {
        $path = parse_url(
            $path,
            PHP_URL_PATH
        );

        if (!is_string($path)) {
            return null;
        }

        $path = '/' . ltrim(
            $path,
            '/'
        );

        foreach ($this->items as $item) {

            foreach ($item['menu'] as $menuItem) {

                $route = '/' . ltrim(
                    $menuItem['route'],
                    '/'
                );

                /*
                 * Correspondance exacte
                 */
                if ($path === $route) {

                    return $item;
                }

                /*
                 * Correspondance avec sous-route
                 */
                if (
                    str_starts_with(
                        $path,
                        rtrim($route, '/')
                        . '/'
                    )
                ) {
                    return $item;
                }
            }
        }

        /*
         * Si aucune entrée interne
         * ne correspond, on cherche
         * une route correspondant
         * au nom du module.
         */
        foreach ($this->items as $item) {

            $module = strtolower(
                $item['module']
            );

            $base = trim(
                (string) (
                    $item['route']
                    ?? ''
                )
            );

            if ($base === '') {
                $base = '/admin/'
                    . strtolower($module);
            }

            $base = '/'
                . ltrim($base, '/');

            if (
                $path === $base
                || str_starts_with(
                    $path,
                    $base . '/'
                )
            ) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Définit le chemin courant.
     */
    public function setCurrentPath(
        string $path
    ): void {
        $this->currentPath = $path;

        $this->currentModule = $this->findByPath(
            $path
        );
    }
    /**
     * Retourne le module courant.
     */
    public function current(): ?array
    {
        return $this->currentModule;
    }
    /**
     * Retourne le menu du module courant.
     */
    public function currentMenu(): array
    {
        if ($this->currentModule === null) {
            return [];
        }

        return $this->currentModule['menu'];
    }
    public function isActive(
        string $route,
        ?string $currentPath = null
    ): bool {
        $currentPath ??= $this->currentPath;

        $route = parse_url(
            $route,
            PHP_URL_PATH
        );

        $currentPath = parse_url(
            $currentPath,
            PHP_URL_PATH
        );

        if (
            !is_string($route)
            || !is_string($currentPath)
        ) {
            return false;
        }

        $route = '/' . ltrim(
            $route,
            '/'
        );

        $currentPath = '/' . ltrim(
            $currentPath,
            '/'
        );

        /*
        * Correspondance exacte
        */
        return $currentPath === $route;
    }

    /**
     * Retourne le chemin des modules.
     */
    public function path(): string
    {
        return $this->modulesPath;
    }

    /**
     * Retourne le chemin courant.
     */
    public function currentPath(): string
    {
        return $this->currentPath;
    }
}