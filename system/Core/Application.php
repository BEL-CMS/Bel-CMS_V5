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
use RuntimeException;
use BelCMS\Core\Errors\ErrorPage;

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
 * Administration
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
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        /*
        * Normalisation des URLs d'administration.
        *
        * /admin
        * /Admin
        * /ADMIN
        * /AdMiN
        *
        * deviennent tous :
        *
        * /admin
        */
        $path = parse_url($uri, PHP_URL_PATH);

        if (is_string($path)) {

            if (
                $path === '/Admin'
                || $path === '/ADMIN'
                || preg_match(
                    '#^/admin(?:/|$)#i',
                    $path
                )
            ) {
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

        ob_start();

        $this->router->dispatch(
            $method,
            $uri
        );

        $content = ob_get_clean();

        /*
        * Administration
        * URL publique : /admin
        * Dossier physique : /administration
        */
        $path = parse_url(
            $uri,
            PHP_URL_PATH
        );

        if (
            is_string($path)
            && (
                $path === '/admin'
                || str_starts_with(
                    $path,
                    '/admin/'
                )
            )
        ) {
            $title = 'Administration';

            require dirname(__DIR__, 2)
                . '/administration/Views/layout.php';

            return;
        }

        /*
        * Site public
        */
        $moduleName = $this->router->getCurrentModule();

        $this->layout->render(
            $content,
            $moduleName
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