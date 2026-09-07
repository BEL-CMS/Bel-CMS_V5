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

        session_start([
            'cookie_httponly' => true,
            'cookie_secure'   => true,
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