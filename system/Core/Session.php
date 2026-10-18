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

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

final class Session
{
    /**
     * Indique si la session est active.
     */
    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Démarre la session.
     */
    public function start(): void
    {
        if ($this->isStarted()) {
            return;
        }

        $secure = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        );

        session_start([
            'cookie_httponly' => true,
            'cookie_secure'   => $secure,
            'cookie_samesite' => 'Strict',
        ]);
    }

    /**
     * Définit une valeur en session.
     */
    public function set(
        string $key,
        mixed $value
    ): void {
        $this->start();

        $_SESSION[$key] = $value;
    }

    /**
     * Récupère une valeur de session.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {
        $this->start();

        return $_SESSION[$key] ?? $default;
    }

    /**
     * Vérifie qu'une clé existe.
     */
    public function has(string $key): bool
    {
        $this->start();

        return array_key_exists(
            $key,
            $_SESSION
        );
    }

    /**
     * Supprime une valeur.
     */
    public function remove(string $key): void
    {
        $this->start();

        unset($_SESSION[$key]);
    }

    /**
     * Supprime toutes les données de session.
     */
    public function clear(): void
    {
        $this->start();

        $_SESSION = [];
    }

    /**
     * Régénère l'identifiant de session.
     */
    public function regenerate(
        bool $deleteOldSession = true
    ): bool {
        $this->start();

        return session_regenerate_id(
            $deleteOldSession
        );
    }

    /**
     * Retourne toutes les données de session.
     */
    public function all(): array
    {
        $this->start();

        return $_SESSION;
    }
}