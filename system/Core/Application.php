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
use BelCMS\Core\Errors\ErrorPage;
use BelCMS\Core\Security\Banishment;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

final class Application
{
    private Router $router;
    private Config $config;
    private BDD $db;
    private ModuleManager $modules;
    private ConfigCMS $configCMS;
    private Groups $groups;
    private View $view;
    private Layout $layout;
    private Assets $assets;
    private Container $container;
    private Language $language;
    private User $user;
    private Session $session;
    private UserSession $userSession;
    private Csrf $csrf;
    private Profils $profils;
    private Banishment $banishment;
    
    public function __construct()
    {
        /*
        * Container
        */
        $this->container = new Container();
        /*
        * Configuration
        */
        $this->config = new Config(
            dirname(__DIR__, 2) . '/config/app.php'
        );
        /*
        * Tables
        */
        require_once dirname(__DIR__, 2) . '/config/tables.php';
        /*
        * Config CMS
        */
        $this->configCMS = new ConfigCMS();
        $this->configCMS->loadConfig();
        $GLOBALS['belcms_config'] = $this->configCMS;
        /*
        * Groupes
        */
        $this->groups = new Groups;
        /*
        * Langue
        */
        $this->language = new Language('fr');
        $this->language->loadGlobal();
        $GLOBALS['belcms_language'] = $this->language;
        /*
        * Session
        */
        $this->session = new Session();
        $this->csrf = new Csrf(
            $this->session
        );
        /*
        * Base de données
        */
        $this->db = new BDD();
        /*
        * Bannissement
        */
        $this->banishment = new Banishment(
            $this->db
        );
        /*
        * Sessions utilisateur
        */
        $this->userSession = new UserSession(
            $this->db
        );
        /*
        * Utilisateur
        */
        $this->user = new User(
            $this->db,
            $this->session,
            $this->userSession
        );
        /*
        * Profils
        */
        $this->profils = new Profils();
        /*
        * View
        */
        $this->view = new View();
        /*
        * Assets
        */
        $this->assets = new Assets();
        /*
        * Plugins globaux
        */
        $this->assets->css('/assets/plugins/fontawesome/all.min.css');
        $this->assets->css('/assets/belcms.css');
        $this->assets->js('/assets/plugins/jquery-4.0.0.min.js');
        $this->assets->js('/assets/belcms.js');
        $this->assets->js('/assets/plugins/fontawesome/all.min.js');
        /*
        * Enregistrement des services
        */
        $this->container->set(
            Session::class,
            $this->session
        );
        $this->container->set(
            BDD::class,
            $this->db
        );
        $this->container->set(
            UserSession::class,
            $this->userSession
        );
        $this->container->set(
            User::class,
            $this->user
        );
        $this->container->set(
            View::class,
            $this->view
        );
        $this->container->set(
            Assets::class,
            $this->assets
        );
        $this->container->set(
            Language::class,
            $this->language
        );
        $this->container->set(
            Csrf::class,
            $this->csrf
        );
        $this->container->set(
            Profils::class,
            $this->profils
        );
        $errorPage = new ErrorPage();
        $this->container->set(
            ErrorPage::class,
            $errorPage
        );
        /*
        * Router
        */
        $this->router = new Router(
            $this->container
        );
        /*
        * Modules
        */
        $this->modules = new ModuleManager(
            $this->router,
            $this->assets,
            $this->language
        );
        $this->container->set(
            ModuleManager::class,
            $this->modules
        );
        $this->modules->loadAll();
        /*
        * ---------------------------------------------------------
        * Menu dynamique de l'administration
        * ---------------------------------------------------------
        */
        $adminMenu = new AdminMenu(
            $this->modules
        );
        $this->container->set(
            AdminMenu::class,
            $adminMenu
        );
        /*
        * ---------------------------------------------------------
        * Administration : routes globales
        * ---------------------------------------------------------
        */
        $adminRoutes = require dirname(__DIR__, 2)
            . '/administration/routes.php';
        foreach ($adminRoutes as $route => $handler) {
            $parts = preg_split(
                '/\s+/',
                trim($route),
                2
            );
            if (
                $parts === false
                || count($parts) !== 2
            ) {
                throw new RuntimeException(
                    "Route d'administration invalide : {$route}"
                );
            }
            $method = strtoupper($parts[0]);
            $path   = $parts[1];
            switch ($method) {
                case 'GET':
                    $this->router->get(
                        $path,
                        $handler,
                        'Administration'
                    );
                    break;
                case 'POST':
                    $this->router->post(
                        $path,
                        $handler,
                        'Administration'
                    );
                    break;
                default:
                    throw new RuntimeException(
                        "Méthode HTTP d'administration non supportée : {$method}"
                    );
            }
        }
        /*
        * ---------------------------------------------------------
        * Administration : routes des modules
        * ---------------------------------------------------------
        *
        * Chaque module peut posséder son propre routes.php :
        *
        * administration/Modules/User/routes.php
        * administration/Modules/News/routes.php
        * administration/Modules/Gallery/routes.php
        *
        * Les routes globales ci-dessus restent inchangées.
        */
        $adminModulesPath = dirname(__DIR__, 2)
            . '/administration/Modules';
        if (is_dir($adminModulesPath)) {
            $directories = scandir($adminModulesPath);
            if ($directories !== false) {
                foreach ($directories as $directory) {
                    if (
                        $directory === '.'
                        || $directory === '..'
                    ) {
                        continue;
                    }
                    $modulePath =
                        $adminModulesPath
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
                    $moduleRoutes = require $routesFile;
                    if (!is_array($moduleRoutes)) {
                        continue;
                    }
                    foreach ($moduleRoutes as $moduleRoute) {
                        if (!is_array($moduleRoute)) {
                            continue;
                        }
                        $method = strtoupper(
                            (string) (
                                $moduleRoute['method']
                                ?? 'GET'
                            )
                        );
                        $path = trim(
                            (string) (
                                $moduleRoute['path']
                                ?? ''
                            )
                        );
                        $handler =
                            $moduleRoute['handler']
                            ?? null;
                        if (
                            $path === ''
                            || $handler === null
                        ) {
                            continue;
                        }
                        switch ($method) {
                            case 'GET':
                                $this->router->get(
                                    $path,
                                    $handler,
                                    $directory
                                );
                                break;
                            case 'POST':
                                $this->router->post(
                                    $path,
                                    $handler,
                                    $directory
                                );
                                break;
                            default:
                                throw new RuntimeException(
                                    "Méthode HTTP non supportée dans le module {$directory} : {$method}"
                                );
                        }
                    }
                }
            }
        }
        /*
        * Layout
        */
        $this->layout = new Layout(
            $this->assets
        );
    }
    /**
     * Retourne le routeur
     */
    public function router(): Router
    {
        return $this->router;
    }
    /**
     * Retourne la configuration
     */
    public function config(): Config
    {
        return $this->config;
    }
    /**
     * Retourne la base de données
     */
    public function db(): BDD
    {
        return $this->db;
    }
    /**
     * Retourne les modules
     */
    public function modules(): ModuleManager
    {
        return $this->modules;
    }
    /**
     * Retourne le moteur de vues
     */
    public function view(): View
    {
        return $this->view;
    }
    /**
     * Retourne les assets
     */
    public function assets(): Assets
    {
        return $this->assets;
    }
    /**
     * Retourne le gestionnaire de langues
     */
    public function language(): Language
    {
        return $this->language;
    }
    /**
     * Retourne le layout
     */
    public function layout(): Layout
    {
        return $this->layout;
    }
    /**
     * Retourne le Container
     */
    public function container(): Container
    {
        $this->container->set(User::class, $this->user);
        return $this->container;
    }
    /**
     * Lance l'application
     */
    public function run(): void
    {
        /*
        * ---------------------------------------------------------
        * Initialisation
        * ---------------------------------------------------------
        */
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';
        /*
        * ---------------------------------------------------------
        * Normalisation des URLs d'administration
        * ---------------------------------------------------------
        *
        * Toutes ces URLs deviennent :
        *
        * /admin
        * /admin/...
        *
        * Exemples :
        *
        * /Admin
        * /ADMIN
        * /AdMiN
        * /ADMIN/news
        *
        * Le changement est effectué AVANT le Router.
        */
        $path = parse_url($uri, PHP_URL_PATH);
        if (is_string($path)) {
            if (preg_match('#^/admin(?:/|$)#i', $path)) {
                $normalizedPath = preg_replace(
                    '#^/admin#i',
                    '/admin',
                    $path
                );
                if (is_string($normalizedPath)) {
                    $query = parse_url(
                        $uri,
                        PHP_URL_QUERY
                    );
                    $uri = $normalizedPath;
                    if (
                        is_string($query)
                        && $query !== ''
                    ) {
                        $uri .= '?' . $query;
                    }
                }
            }
        }
        /*
        * Vérification du bannissement avant le routage.
        */
        $ipAddress  = $_SERVER['REMOTE_ADDR'] ?? null;
        $hashKey    = $this->user->hashKey();
        $banishment = $this->banishment->findActive(
            $hashKey,
            is_string($ipAddress) ? $ipAddress : null
        );
        if ($banishment !== null) {
            http_response_code(403);

            header('Content-Type: text/html; charset=UTF-8');
            header('Cache-Control: no-store');
            $reason = (string) ($banishment->reason ?? 'Aucun motif communiqué.');
            $expiresAt = isset($banishment->expires_at)
                ? (string) $banishment->expires_at
                : null;
            $banishmentKey = isset($banishment->banishment_key)
                ? (string) $banishment->banishment_key
                : null;

            require dirname(__DIR__)
                . '/Core/Security/Views/banished.php';
            exit;
        }
        /*
        * ---------------------------------------------------------
        * Dispatch du Router
        * ---------------------------------------------------------
        */
        ob_start();
        $this->router->dispatch(
            $method,
            $uri
        );
        $content = ob_get_clean();
        /*
        * ---------------------------------------------------------
        * Chemin actuel
        * ---------------------------------------------------------
        */
        $path = parse_url(
            $uri,
            PHP_URL_PATH
        );
        /*
        * ---------------------------------------------------------
        * Administration
        * ---------------------------------------------------------
        */
        if (is_string($path) && ($path === '/admin' || str_starts_with($path, '/admin/'))) {
            $title = 'Administration';
            $adminMenu = $this->container->get(
                AdminMenu::class
            );
            require dirname(__DIR__, 2). '/administration/Views/layout.php';
            return;
        }
        /*
        * ---------------------------------------------------------
        * Site public
        * ---------------------------------------------------------
        */
        $title = 'Bel-CMS';

        $this->layout->render(
            $content,
            $title
        );
    }
    public function user(): User
    {
        return $this->user;
    }
    public function session(): Session
    {
        return $this->session;
    }
    public function userSession(): UserSession
    {
        return $this->userSession;
    }
}