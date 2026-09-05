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

class Router
{
    private array $routes = [];

    private Container $container;

    public function __construct(
        Container $container
    ) {
        $this->container = $container;
    }

    /**
     * Route GET
     */
    public function get(
        string $path,
        array|callable $handler,
        ?string $module = null
    ): void {
        $this->add(
            'GET',
            $path,
            $handler,
            $module
        );
    }

    /**
     * Route POST
     */
    public function post(
        string $path,
        array|callable $handler,
        ?string $module = null
    ): void {
        $this->add(
            'POST',
            $path,
            $handler,
            $module
        );
    }

    /**
     * Ajoute une route
     */
    private function add(
        string $method,
        string $path,
        array|callable $handler,
        ?string $module = null
    ): void {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler,
            'module'  => $module,
        ];
    }

    /**
     * Exécute la route demandée
     */
    public function dispatch(string $method,string $uri): mixed 
    {
        $method = strtoupper($method);

        $uri = parse_url(
            $uri,
            PHP_URL_PATH
        );

        $uri = '/'
            . trim(
                (string) $uri,
                '/'
            );

        if ($uri !== '/') {
            $uri = rtrim(
                $uri,
                '/'
            );
        }

        foreach ($this->routes as $route) {

            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match(
                $route['path'],
                $uri
            );

            if ($params === false) {
                continue;
            }

            $this->loadRouteAssets(
                $route['module'] ?? null
            );

            return $this->callHandler(
                $route['handler'],
                $params
            );
        }

        return $this->notFound();
    }

    /**
     * Vérifie si une route correspond
     */
    private function match(
        string $routePath,
        string $requestPath
    ): array|false {
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static function (
                array $matches
            ): string {
                return '(?P<'
                    . $matches[1]
                    . '>[^/]+)';
            },
            $routePath
        );

        if ($pattern === null) {
            return false;
        }

        $pattern = '#^'
            . $pattern
            . '$#i';

        if (
            !preg_match(
                $pattern,
                $requestPath,
                $matches
            )
        ) {
            return false;
        }

        $params = [];

        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    /**
     * Appelle le handler
     */
    private function callHandler(
        array|callable $handler,
        array $params
    ): mixed {

        /*
         * Fonction ou Closure
         */
        if (
            is_callable($handler)
            && !is_array($handler)
        ) {
            return call_user_func_array(
                $handler,
                array_values($params)
            );
        }

        /*
         * [Controller::class, 'method']
         */
        if (
            is_array($handler)
            && count($handler) === 2
        ) {
            [
                $controller,
                $method
            ] = $handler;

            /*
             * Nom de classe
             */
            if (is_string($controller)) {

                $controller = $this->makeController(
                    $controller
                );
            }

            if (
                !is_object($controller)
                || !method_exists(
                    $controller,
                    $method
                )
            ) {
                throw new RuntimeException(
                    "La méthode {$method} n'existe pas."
                );
            }

            return call_user_func_array(
                [
                    $controller,
                    $method
                ],
                array_values($params)
            );
        }

        throw new RuntimeException(
            'Handler de route invalide.'
        );
    }

    /**
     * Crée un contrôleur
     *
     * Résout automatiquement :
     *
     * Model
     * View
     * BDD
     * etc.
     */
    private function makeController(
        string $class
    ): object {

        /*
         * Récupération de la réflexion
         */
        $reflection = new \ReflectionClass(
            $class
        );

        $constructor = $reflection->getConstructor();

        /*
         * Pas de constructeur
         */
        if ($constructor === null) {
            return new $class();
        }

        /*
         * Analyse des paramètres
         */
        $arguments = [];

        foreach (
            $constructor->getParameters()
            as $parameter
        ) {

            $type = $parameter->getType();

            /*
             * Paramètre sans type
             */
            if (
                !$type
                || $type->isBuiltin()
            ) {
                if (
                    $parameter->isDefaultValueAvailable()
                ) {
                    $arguments[] =
                        $parameter->getDefaultValue();

                    continue;
                }

                throw new RuntimeException(
                    'Impossible de résoudre le paramètre '
                    . $parameter->getName()
                    . ' du contrôleur '
                    . $class
                );
            }

            $typeName = $type->getName();

            /*
             * Le service existe déjà dans le Container
             */
            if (
                $this->container->has(
                    $typeName
                )
            ) {
                $arguments[] =
                    $this->container->get(
                        $typeName
                    );

                continue;
            }

            /*
             * Le Model du module
             *
             * Exemple :
             * BelCMS\Modules\News\Model
             */
            $arguments[] = $this->make(
                $typeName
            );
        }

        return $reflection->newInstanceArgs(
            $arguments
        );
    }

    /**
     * Création automatique d'une classe
     */
    private function make(
        string $class
    ): object {

        /*
         * Si déjà présent dans le Container
         */
        if (
            $this->container->has($class)
        ) {
            return $this->container->get(
                $class
            );
        }

        $reflection = new \ReflectionClass(
            $class
        );

        $constructor =
            $reflection->getConstructor();

        /*
         * Classe sans constructeur
         */
        if ($constructor === null) {
            return new $class();
        }

        $arguments = [];

        foreach (
            $constructor->getParameters()
            as $parameter
        ) {

            $type = $parameter->getType();

            if (
                !$type
                || $type->isBuiltin()
            ) {

                if (
                    $parameter->isDefaultValueAvailable()
                ) {
                    $arguments[] =
                        $parameter->getDefaultValue();

                    continue;
                }

                throw new RuntimeException(
                    'Impossible de résoudre le paramètre '
                    . $parameter->getName()
                    . ' de '
                    . $class
                );
            }

            $typeName = $type->getName();

            if (
                $this->container->has(
                    $typeName
                )
            ) {
                $arguments[] =
                    $this->container->get(
                        $typeName
                    );

                continue;
            }

            $arguments[] = $this->make(
                $typeName
            );
        }

        return $reflection->newInstanceArgs(
            $arguments
        );
    }

    /**
     * 404
     */
    private function notFound(): never
    {
        http_response_code(404);

        require dirname(__DIR__)
            . '/Core/Errors/404.php';

        exit;
    }

    /**
     * Retourne les routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

private function loadRouteAssets(
    ?string $module
): void {
    if ($module === null) {
        return;
    }

    $moduleManager = $this->container->get(
        ModuleManager::class
    );

    if (!$moduleManager instanceof ModuleManager) {
        return;
    }

    $config = $moduleManager->get(
        $module
    );

    if ($config === null) {
        return;
    }

    $assets = $config['assets'] ?? [];

    if (
        ($assets['css'] ?? false) === true
    ) {
        $assetsService = $this->container->get(
            Assets::class
        );

        $assetsService->moduleCss(
            $module
        );
    }

    if (
        ($assets['js'] ?? false) === true
    ) {
        $assetsService = $this->container->get(
            Assets::class
        );

        $assetsService->moduleJs(
            $module
        );
    }
}
}