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

use BelCMS\Core\Security\TOTP\ProvisioningUri;
use BelCMS\Core\Security\TOTP\TOTP;

final class User
{
    private BDD $db;
    /**
     * Utilisateur actuellement connecté.
     */
    private ?object $currentUser = null;
    /**
     * Indique si nous avons déjà tenté de charger l'utilisateur.
     */
    private bool $loaded = false;
    /**
     * Clé utilisée dans la session.
     */
    private const SESSION_KEY = 'BELCMS_USER_ID';

    public function __construct(BDD $db)
    {
        $this->db = $db;
    }

    /**
     * Retourne l'utilisateur actuellement connecté.
     */
    public function current(): ?object
    {
        if ($this->loaded) {
            return $this->currentUser;
        }

        $this->loaded = true;

        $userId = $_SESSION[self::SESSION_KEY] ?? null;

        if ($userId === null || !is_numeric($userId)) {
            return null;
        }

        $this->db->table('belcms_user');

        $this->db->where([
            'name'  => 'id',
            'value' => (int) $userId
        ]);

        $this->db->queryOne();

        $this->currentUser = $this->db->data ?? null;

        return $this->currentUser;
    }

    /**
     * Vérifie si un utilisateur est connecté.
     */
    public function isLogged(): bool
    {
        return $this->current() !== null;
    }

    /**
     * Retourne l'ID de l'utilisateur connecté.
     */
    public function id(): ?int
    {
        $user = $this->current();

        if (!$user) {
            return null;
        }

        return isset($user->id)
            ? (int) $user->id
            : null;
    }

    /**
     * Retourne le nom d'utilisateur.
     */
    public function username(): ?string
    {
        $user = $this->current();

        if (!$user || !isset($user->username)) {
            return null;
        }

        return (string) $user->username;
    }

    /**
     * Retourne l'adresse email.
     */
    public function email(): ?string
    {
        $user = $this->current();

        if (!$user || !isset($user->email)) {
            return null;
        }

        return (string) $user->email;
    }
    /**
     * Vérifie si le 2FA est activé.
     */
    public function isTwoFactorEnabled(): bool
    {
        return (bool) $this->get(
            'two_factor_enabled',
            false
        );
    }

    /**
     * Retourne le secret 2FA.
     */
    public function getTwoFactorSecret(): ?string
    {
        $secret = $this->get(
            'two_factor_secret',
            null
        );

        if (!is_string($secret) || $secret === '') {
            return null;
        }

        return $secret;
    }

    /**
     * Génère un nouveau secret TOTP.
     */
    public function generateTwoFactorSecret(): string
    {
        return TOTP::generateSecret();
    }

    /**
     * Génère l'URI compatible avec les applications TOTP.
     */
    public function getTwoFactorUri(
        string $account,
        ?string $secret = null
    ): ?string {

        $secret ??= $this->getTwoFactorSecret();

        if ($secret === null || $secret === '') {
            return null;
        }

        return ProvisioningUri::create(
            $secret,
            $account,
            'Bel-CMS'
        );
    }

    /**
     * Vérifie un code TOTP.
     */
    public function verifyTwoFactorCode(
        string $code,
        ?string $secret = null
    ): bool {

        $secret ??= $this->getTwoFactorSecret();

        if ($secret === null || $secret === '') {
            return false;
        }

        $code = trim($code);

        if ($code === '') {
            return false;
        }

        return TOTP::verify(
            $secret,
            $code
        );
    }

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public function isAdmin(): bool
    {
        $user = $this->current();

        if (!$user || !isset($user->admin)) {
            return false;
        }

        return (bool) $user->admin;
    }

    /**
     * Vérifie si l'utilisateur est root.
     */
    public function isRoot(): bool
    {
        $user = $this->current();

        if (!$user || !isset($user->root)) {
            return false;
        }

        return (bool) $user->root;
    }

    /**
     * Authentifie un utilisateur.
     * @return bool True si l'authentification réussit.
     */
    public function login(
        string $identifier,
        string $password
    ): bool {

        $identifier = trim($identifier);

        if ($identifier === '' || $password === '') {
            return false;
        }

        /*
        * Recherche par email.
        */
        $this->db->table('belcms_user');

        $this->db->where([
            'name'  => 'email',
            'value' => $identifier
        ]);

        $this->db->queryOne();

        $user = $this->db->data ?? null;

        /*
        * Si aucun utilisateur n'est trouvé,
        * recherche par username.
        */
        if (!$user) {

            $this->db->table('belcms_user');

            $this->db->where([
                'name'  => 'username',
                'value' => $identifier
            ]);

            $this->db->queryOne();

            $user = $this->db->data ?? null;
        }

        /*
        * Utilisateur introuvable.
        */
        if (!$user) {
            return false;
        }

        /*
        * Vérification du mot de passe.
        */
        if (
            !isset($user->password) ||
            !password_verify($password, $user->password)
        ) {
            return false;
        }

        /*
        * Compte non validé.
        */
        if (
            !isset($user->valid) ||
            !(bool) $user->valid
        ) {
            return false;
        }

        /*
        * Protection contre la fixation de session.
        */
        session_regenerate_id(true);

        /*
        * Enregistre l'utilisateur dans la session.
        */
        $_SESSION[self::SESSION_KEY] = (int) $user->id;

        /*
        * Réinitialise le cache.
        */
        $this->loaded = false;
        $this->currentUser = null;

        /*
        * Recharge l'utilisateur.
        */
        $this->current();

        return true;
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        unset(
            $_SESSION[self::SESSION_KEY]
        );

        $this->loaded = true;
        $this->currentUser = null;
    }

    public function get(string $property, mixed $default = null): mixed
    {
        $user = $this->current();

        if (!$user) {
            return $default;
        }

        return $user->{$property} ?? $default;
    }

    /**
     * Active le 2FA après validation du code.
     */
    public function enableTwoFactor(
        string $secret,
        string $code
    ): bool {

        if (!$this->isLogged()) {
            return false;
        }

        $secret = trim($secret);
        $code   = trim($code);

        if ($secret === '' || $code === '') {
            return false;
        }

        /*
        * Vérifie que le code correspond au secret.
        */
        if (!TOTP::verify($secret, $code)) {
            return false;
        }

        $userId = $this->id();

        if ($userId === null) {
            return false;
        }

        /*
        * Enregistre le secret et active le 2FA.
        */
        $this->db->table('belcms_user');

        $this->db->where([
            'name'  => 'id',
            'value' => $userId
        ]);

        $result = $this->db->update([
            'two_factor_secret'  => $secret,
            'two_factor_enabled' => 1
        ]);

        if (!$result) {
            return false;
        }

        /*
        * Met à jour l'utilisateur déjà chargé en mémoire.
        */
        $this->currentUser->two_factor_secret  = $secret;
        $this->currentUser->two_factor_enabled = 1;

        return true;
    }

    /**
     * Désactive le 2FA.
     */
    public function disableTwoFactor(): bool
    {
        if (!$this->isLogged()) {
            return false;
        }

        $userId = $this->id();

        if ($userId === null) {
            return false;
        }

        $this->db->table('belcms_user');

        $this->db->where([
            'name'  => 'id',
            'value' => $userId
        ]);

        $result = $this->db->update([
            'two_factor_enabled' => 0,
            'two_factor_secret'  => null
        ]);

        if (!$result) {
            return false;
        }

        $this->currentUser->two_factor_enabled = 0;
        $this->currentUser->two_factor_secret  = null;

        return true;
    }

    /**
     * Vérifie si le compte est validé.
     */
    public function valid(): bool
    {
        return (bool) $this->get(
            'valid',
            false
        );
    }

    /**
     * Retourne l'IP enregistrée.
     */
    public function ip(): ?string
    {
        $ip = $this->get('ip');

        if ($ip === null || $ip === '') {
            return null;
        }

        return (string) $ip;
    }

    /**
     * Retourne la clé hash de l'utilisateur.
     */
    public function hashKey(): ?string
    {
        $hashKey = $this->get('hash_key');

        if (!is_string($hashKey) || $hashKey === '') {
            return null;
        }

        return $hashKey;
    }

    /**
     * Retourne le token de sécurité s'il existe.
     */
    public function token(): ?string
    {
        $token = $this->get('token');

        if (!is_string($token) || $token === '') {
            return null;
        }

        return $token;
    }

    /**
     * Retourne les données de l'utilisateur connecté.
     */
    public function data(): ?object
    {
        return $this->current();
    }

    /**
     * Exige qu'un utilisateur soit connecté.
     *
     * Retourne true si l'utilisateur est connecté.
     */
    public function requireLogin(): bool
    {
        return $this->isLogged();
    }

    /**
     * Recharge les données de l'utilisateur courant.
     */
    public function reload(): ? object
    {
        $this->loaded = false;
        $this->currentUser = null;
        return $this->current();
    }

    
}