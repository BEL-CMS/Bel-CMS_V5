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

        $hashKey = trim($hashKey);
        $sessionId = trim($sessionId);

        if ($hashKey === '' || $sessionId === '') {
            return false;
        }

        /*
        * Expiration par défaut : 30 jours
        */
        if ($expiresAt === null) {
            $expiresAt = date(
                'Y-m-d H:i:s',
                strtotime('+30 days')
            );
        }

        $existing = $this->getBySessionId($sessionId);

        if ($existing) {
            return $this->updateActivity($sessionId);
        }

        $this->db->table('TABLE_SESSION');

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
            'TABLE_SESSION'
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
            'TABLE_SESSION'
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
            'TABLE_SESSION'
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
            'TABLE_SESSION'
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

        if ($hashKey === '' || $currentSessionId === '') {
            return 0;
        }

        // Sécurité : la session PHP courante doit réellement
        // appartenir à l'utilisateur demandé avant toute suppression.
        $currentSession = $this->getBySessionId($currentSessionId);

        if (!is_object($currentSession)) {
            return 0;
        }

        $dbHashKey = trim((string)($currentSession->hash_key ?? ''));

        if ($dbHashKey === '' || !hash_equals($dbHashKey, $hashKey)) {
            return 0;
        }

        $sessions = $this->getByHashKey($hashKey);
        $deleted = 0;

        foreach ($sessions as $session) {
            $sessionId = trim((string)($session->session_id ?? ''));

            if ($sessionId === '') {
                continue;
            }

            // La session PHP actuelle est TOUJOURS conservée.
            if (hash_equals($sessionId, $currentSessionId)) {
                continue;
            }

            if ($this->delete($sessionId)) {
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
    public function current(?string $hashKey = null): mixed
    {
        $this->cleanup(30);

        $sessionId = session_id();

        if ($sessionId === '') {
            return null;
        }

        /*
        * Recherche de la session actuelle
        */
        $session = $this->getBySessionId($sessionId);

        if ($session) {

            /*
            * Vérification de l'expiration
            */
            if ($this->isExpired($session)) {

                $this->delete($sessionId);

                return null;
            }

            /*
            * Session encore valide
            */
            $this->updateActivity($sessionId);

            return $this->getBySessionId($sessionId);
        }

        /*
        * Aucune session trouvée en BDD
        *
        * On en crée une si on connaît
        * l'utilisateur courant.
        */
        if (
            $hashKey === null
            || trim($hashKey) === ''
        ) {
            return null;
        }

        $created = $this->create(
            $hashKey,
            $sessionId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        if (!$created) {
            return null;
        }

        return $this->getBySessionId($sessionId);
    }
    public function cleanup(int $days = 30): int
    {
        $deleted = 0;
        /*
        * Sessions sans activité depuis X jours
        */
        $lastActivity = date(
            'Y-m-d H:i:s',
            strtotime("-{$days} days")
        );
        $this->db->table('TABLE_SESSION');

        $this->db->where([
            'name'     => 'last_activity',
            'value'    => $lastActivity,
            'op'       => '<'
        ]);

        $deleted += (int)$this->db->delete();
        /*
        * Sessions dont la date d'expiration est dépassée
        */
        $now = date('Y-m-d H:i:s');

        $this->db->table('TABLE_SESSION');

        $this->db->where([
            'name'     => 'expires_at',
            'value'    => $now,
            'op'       => '<'
        ]);

        $deleted += (int)$this->db->delete();


        return $deleted;
    }

    public function isExpired(object $session): bool
    {
        if (
            !isset($session->expires_at)
            || $session->expires_at === null
            || $session->expires_at === ''
        ) {
            return false;
        }

        return strtotime((string)$session->expires_at) <= time();
    }

}
