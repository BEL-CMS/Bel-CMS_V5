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

final class UserSession
{
    private BDD $db;

    public function __construct(BDD $db)
    {
        $this->db = $db;
    }

    /**
     * Crée une session utilisateur en BDD.
     */
    public function create(
        string $hashKey,
        string $sessionId,
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $expiresAt = null
    ): bool {
        $hashKey   = trim($hashKey);
        $sessionId = trim($sessionId);

        if (
            $hashKey === '' ||
            $sessionId === ''
        ) {
            return false;
        }

        $this->db->table(
            'belcms_user_sessions'
        );

        return $this->db->insert([
            'hash_key'      => $hashKey,
            'session_id'    => $sessionId,
            'ip'            => $ip,
            'user_agent'    => $userAgent,
            'expires_at'    => $expiresAt,
            'last_activity' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Retourne une session par son session_id.
     */
    public function getBySessionId(
        string $sessionId
    ): mixed {
        $sessionId = trim($sessionId);

        if ($sessionId === '') {
            return null;
        }

        $this->db->table(
            'belcms_user_sessions'
        );

        $this->db->where([
            'name'  => 'session_id',
            'value' => $sessionId
        ]);

        $this->db->queryOne();

        return $this->db->data ?? null;
    }

    /**
     * Retourne les sessions d'un utilisateur.
     */
    public function getByHashKey(
        string $hashKey
    ): array {
        $hashKey = trim($hashKey);

        if ($hashKey === '') {
            return [];
        }

        $this->db->table(
            'belcms_user_sessions'
        );

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        $this->db->orderby('last_activity', true);

        $this->db->queryAll();

        return $this->db->data ?? [];
    }

    /**
     * Met à jour la dernière activité.
     */
    public function updateActivity(
        string $sessionId
    ): bool {
        $sessionId = trim($sessionId);

        if ($sessionId === '') {
            return false;
        }

        $this->db->table(
            'belcms_user_sessions'
        );

        $this->db->where([
            'name'  => 'session_id',
            'value' => $sessionId
        ]);

        return $this->db->update([
            'last_activity' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Supprime une session.
     */
    public function delete(
        string $sessionId
    ): bool {
        $sessionId = trim($sessionId);

        if ($sessionId === '') {
            return false;
        }

        $this->db->table(
            'belcms_user_sessions'
        );

        $this->db->where([
            'name'  => 'session_id',
            'value' => $sessionId
        ]);

        return $this->db->delete();
    }

    /**
     * Supprime toutes les sessions
     * d'un utilisateur sauf celle indiquée.
     */
    public function deleteOthers(
        string $hashKey,
        string $currentSessionId
    ): int {
        $hashKey = trim($hashKey);
        $currentSessionId = trim($currentSessionId);

        if (
            $hashKey === '' ||
            $currentSessionId === ''
        ) {
            return 0;
        }

        $sessions = $this->getByHashKey(
            $hashKey
        );

        $deleted = 0;

        foreach ($sessions as $session) {

            if (
                !isset($session->session_id) ||
                $session->session_id === $currentSessionId
            ) {
                continue;
            }

            if (
                $this->delete(
                    (string) $session->session_id
                )
            ) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Supprime toutes les sessions d'un utilisateur.
     */
    public function deleteAll(
        string $hashKey
    ): int {
        $hashKey = trim($hashKey);

        if ($hashKey === '') {
            return 0;
        }

        $sessions = $this->getByHashKey(
            $hashKey
        );

        $deleted = 0;

        foreach ($sessions as $session) {

            if (
                !isset($session->session_id)
            ) {
                continue;
            }

            if (
                $this->delete(
                    (string) $session->session_id
                )
            ) {
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Retourne ou initialise la session PHP actuelle.
     */
    public function current(
        ?string $hashKey = null
    ): mixed {
        $sessionId = session_id();

        if ($sessionId === '') {
            return null;
        }

        /*
        * Recherche de la session en BDD.
        */
        $session = $this->getBySessionId(
            $sessionId
        );

        /*
        * La session existe déjà.
        */
        if ($session) {

            $this->updateActivity(
                $sessionId
            );

            return $this->getBySessionId(
                $sessionId
            );
        }

        /*
        * Impossible de créer une session
        * sans hash_key utilisateur.
        */
        if (
            $hashKey === null ||
            trim($hashKey) === ''
        ) {
            return null;
        }

        /*
        * Création de la session.
        */
        $this->create(
            $hashKey,
            $sessionId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        return $this->getBySessionId(
            $sessionId
        );
    }

}