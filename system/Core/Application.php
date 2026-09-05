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

final class Application
{
    private Router $router;
    private Config $config;
    private BDD $db;
    private ModuleManager $modules;
    private View $view;
    private Layout $layout;
    private Assets $assets;
    private Container $container;
    private Language $language;
    private User $user;

    public function __construct()
    {
        /*
         * -------------------------------------------------
         * Container
         * -------------------------------------------------
         */
        $this->container = new Container();
        /*
         * -------------------------------------------------
         * Configuration
         * -------------------------------------------------
         */
        $this->config = new Config(
            dirname(__DIR__, 2) . '/config/app.php'
        );
        /*
         * -------------------------------------------------
         * Tables
         * -------------------------------------------------
         */
        require_once dirname(__DIR__, 2) . '/config/tables.php';
        /*
         * -------------------------------------------------
         * Langue
         * -------------------------------------------------
         */
        $this->language = new Language('fr');
        /*
         * Chargement de la langue globale
         */
        $this->language->loadGlobal();
        /*
         * Disponible pour la fonction __()
         */
        $GLOBALS['belcms_language'] = $this->language;
        /*
         * -------------------------------------------------
         * Base de données
         * -------------------------------------------------
         */
        $this->db = new BDD();

        $this->user = new User($this->db);
        $this->view = new View();
        $this->assets = new Assets();
        $this->container->set(User::class, $this->user);

        /*
         * -------------------------------------------------
         * Moteur de vues
         * -------------------------------------------------
         */
        $this->view = new View();
        /*
         * -------------------------------------------------
         * Gestion des assets
         * -------------------------------------------------
         */
        $this->assets = new Assets();
        /*
        * -------------------------------------------------
        * Assets globaux Bel-CMS
        * -------------------------------------------------
        */
        $this->assets->css('/assets/belcms.css');
        $this->assets->js('/assets/plugins/jquery-4.0.0.min.js');
        $this->assets->js('/assets/belcms.js');
        /*
         * -------------------------------------------------
         * Enregistrement des services
         * -------------------------------------------------
         */
        $this->container->set(
            BDD::class,
            $this->db
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
        /*
         * -------------------------------------------------
         * Routeur
         * -------------------------------------------------
         */
        $this->router = new Router(
            $this->container
        );
        /*
         * -------------------------------------------------
         * Gestionnaire de modules
         * -------------------------------------------------
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
        /*
         * Chargement automatique des modules
         */
        $this->modules->loadAll();
        /*
         * -------------------------------------------------
         * Layout
         * -------------------------------------------------
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

        ob_start();

        $this->router->dispatch(
            $method,
            $uri
        );

        $content = ob_get_clean();

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
}