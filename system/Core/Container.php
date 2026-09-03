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

final class Container
{
    private array $instances = [];

    /**
     * Enregistre une instance
     */
    public function set(
        string $name,
        object $instance
    ): void {
        $this->instances[$name] = $instance;
    }

    /**
     * Récupère une instance
     */
    public function get(string $name): object
    {
        if (!isset($this->instances[$name])) {
            throw new RuntimeException(
                "Service introuvable : {$name}"
            );
        }

        return $this->instances[$name];
    }

    /**
     * Vérifie si un service existe
     */
    public function has(string $name): bool
    {
        return isset($this->instances[$name]);
    }
}