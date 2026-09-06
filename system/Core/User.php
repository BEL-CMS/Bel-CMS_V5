<?php

declare(strict_types=1);

namespace BelCMS\Core;

use BelCMS\Core\Security\RecoveryCode;
use BelCMS\Core\Security\TOTP\ProvisioningUri;
use BelCMS\Core\Security\TOTP\TOTP;

final class User
{
    private BDD $db;
    private Session $session;

    /**
     * Utilisateur actuellement connecté.
     */
    private ?object $currentUser = null;

    /**
     * Indique si nous avons déjà tenté de charger l'utilisateur.
     */
    private bool $loaded = false;

    /**
     * Clé de session de l'utilisateur connecté.
     */
    private const SESSION_KEY = 'BELCMS_USER_HASH_KEY';

    /**
     * Clé de session utilisée lorsque le 2FA est en attente.
     */
    private const TWO_FACTOR_PENDING_KEY = 'BELCMS_2FA_PENDING';

    /**
     * Clé temporaire utilisée pendant la configuration du 2FA.
     */
    private const TWO_FACTOR_SETUP_KEY = 'BELCMS_2FA_SETUP_SECRET';

    public function __construct(
        BDD $db,
        Session $session
    ) {
        $this->db      = $db;
        $this->session = $session;
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

        /*
         * Récupération du hash_key depuis la session.
         */
        $hashKey = $this->session->get(
            self::SESSION_KEY
        );

        if (
            !is_string($hashKey) ||
            $hashKey === ''
        ) {
            return null;
        }

        /*
         * Recherche de l'utilisateur par hash_key.
         */
        $this->db->table('belcms_user');

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        $this->db->queryOne();

        $this->currentUser = $this->db->data ?? null;

        /*
         * Si l'utilisateur n'existe plus,
         * on nettoie la session.
         */
        if ($this->currentUser === null) {
            $this->session->remove(
                self::SESSION_KEY
            );
        }

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
     * Retourne l'ID technique AUTO_INCREMENT.
     *
     * Conservé pour les opérations internes BDD.
     */
    public function id(): ?int
    {
        $user = $this->current();

        if (
            !$user ||
            !isset($user->id)
        ) {
            return null;
        }

        return (int) $user->id;
    }

    /**
     * Retourne le hash_key de l'utilisateur.
     *
     * Identifiant métier Bel-CMS.
     */
    public function hashKey(): ?string
    {
        $hashKey = $this->get('hash_key');

        if (
            !is_string($hashKey) ||
            $hashKey === ''
        ) {
            return null;
        }

        return $hashKey;
    }

    /**
     * Retourne le nom d'utilisateur.
     */
    public function username(): ?string
    {
        $username = $this->get('username');

        if (!is_string($username)) {
            return null;
        }

        return $username;
    }

    /**
     * Retourne l'adresse email.
     */
    public function email(): ?string
    {
        $email = $this->get('email');

        if (!is_string($email)) {
            return null;
        }

        return $email;
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

        if (
            $ip === null ||
            $ip === ''
        ) {
            return null;
        }

        return (string) $ip;
    }

    /**
     * Retourne le token de sécurité.
     */
    public function token(): ?string
    {
        $token = $this->get('token');

        if (
            !is_string($token) ||
            $token === ''
        ) {
            return null;
        }

        return $token;
    }

    /**
     * Retourne toutes les données
     * de l'utilisateur connecté.
     */
    public function data(): ?object
    {
        return $this->current();
    }

    /**
     * Retourne une propriété de l'utilisateur.
     */
    public function get(
        string $property,
        mixed $default = null
    ): mixed {
        $user = $this->current();

        if (!$user) {
            return $default;
        }

        return $user->{$property} ?? $default;
    }

    /**
     * Recharge les données de l'utilisateur.
     */
    public function reload(): ?object
    {
        $this->loaded = false;
        $this->currentUser = null;

        return $this->current();
    }

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->get(
            'admin',
            false
        );
    }

    /**
     * Vérifie si l'utilisateur est root.
     */
    public function isRoot(): bool
    {
        return (bool) $this->get(
            'root',
            false
        );
    }

    /*
     * =========================================================
     * AUTHENTIFICATION
     * =========================================================
     */

    /**
     * Authentifie un utilisateur.
     */
    public function login(
        string $identifier,
        string $password
    ): bool {
        $identifier = trim($identifier);

        if (
            $identifier === '' ||
            $password === ''
        ) {
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
         * Recherche par username.
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
            !password_verify(
                $password,
                $user->password
            )
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
         * Le hash_key doit exister.
         */
        if (
            !isset($user->hash_key) ||
            !is_string($user->hash_key) ||
            $user->hash_key === ''
        ) {
            return false;
        }

        /*
         * Nouvelle session.
         */
        if (!$this->session->regenerate(true)) {
            return false;
        }

        /*
         * 2FA activé :
         * aucune connexion complète pour l'instant.
         */
        if (
            isset($user->two_factor_enabled) &&
            (bool) $user->two_factor_enabled
        ) {

            $this->setTwoFactorPending(
                $user->hash_key
            );

            return true;
        }

        /*
         * Connexion classique.
         */
        $this->session->set(
            self::SESSION_KEY,
            $user->hash_key
        );

        /*
         * Nettoyage d'une éventuelle attente 2FA.
         */
        $this->clearTwoFactorPending();

        $this->loaded = false;
        $this->currentUser = null;

        $this->current();

        return true;
    }

    /**
     * Finalise la connexion après validation du 2FA.
     */
    public function completeTwoFactorLogin(): bool
    {
        $hashKey = $this->getTwoFactorPendingHashKey();

        if (
            $hashKey === null ||
            $hashKey === ''
        ) {
            return false;
        }

        /*
         * Vérification que l'utilisateur existe toujours.
         */
        $this->db->table('belcms_user');

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        $this->db->queryOne();

        $user = $this->db->data ?? null;

        if (!$user) {
            $this->clearTwoFactorPending();

            return false;
        }

        /*
         * Nouvelle rotation de session.
         */
        if (!$this->session->regenerate(true)) {
            return false;
        }

        /*
         * Connexion définitive.
         */
        $this->session->set(
            self::SESSION_KEY,
            $hashKey
        );

        /*
         * Suppression de l'attente.
         */
        $this->clearTwoFactorPending();

        /*
         * Recharge l'utilisateur.
         */
        $this->loaded = false;
        $this->currentUser = null;

        return $this->current() !== null;
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(): void
    {
        if (!$this->session->isStarted()) {
            return;
        }

        $this->session->remove(
            self::SESSION_KEY
        );

        $this->clearTwoFactorPending();

        $this->session->remove(
            self::TWO_FACTOR_SETUP_KEY
        );

        $this->loaded = true;
        $this->currentUser = null;
    }

    /**
     * Exige un utilisateur connecté.
     */
    public function requireLogin(): bool
    {
        return $this->isLogged();
    }

    /*
     * =========================================================
     * 2FA
     * =========================================================
     */

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
            'two_factor_secret'
        );

        if (
            !is_string($secret) ||
            $secret === ''
        ) {
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
     * Génère l'URI TOTP.
     */
    public function getTwoFactorUri(
        string $account,
        ?string $secret = null
    ): ?string {
        $secret ??= $this->getTwoFactorSecret();

        if (
            $secret === null ||
            $secret === ''
        ) {
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

        if (
            $secret === null ||
            $secret === ''
        ) {
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

        if (
            $secret === '' ||
            $code === ''
        ) {
            return false;
        }

        if (!TOTP::verify($secret, $code)) {
            return false;
        }

        $hashKey = $this->hashKey();

        if ($hashKey === null) {
            return false;
        }

        $this->db->table('belcms_user');

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        $result = $this->db->update([
            'two_factor_secret'  => $secret,
            'two_factor_enabled' => 1
        ]);

        if (!$result) {
            return false;
        }

        if ($this->currentUser !== null) {
            $this->currentUser->two_factor_secret  = $secret;
            $this->currentUser->two_factor_enabled = 1;
        }

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

        $hashKey = $this->hashKey();

        if ($hashKey === null) {
            return false;
        }

        $this->db->table('belcms_user');

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        $result = $this->db->update([
            'two_factor_enabled' => 0,
            'two_factor_secret'  => null
        ]);

        if (!$result) {
            return false;
        }

        if ($this->currentUser !== null) {
            $this->currentUser->two_factor_enabled = 0;
            $this->currentUser->two_factor_secret  = null;
        }

        return true;
    }

    /*
     * =========================================================
     * 2FA PENDING
     * =========================================================
     */

    /**
     * Indique si une authentification 2FA est en attente.
     */
    public function isTwoFactorPending(): bool
    {
        return $this->session->has(
            self::TWO_FACTOR_PENDING_KEY
        );
    }

    /**
     * Place un utilisateur en attente 2FA.
     */
    public function setTwoFactorPending(
        string $hashKey
    ): void {
        $hashKey = trim($hashKey);

        if ($hashKey === '') {
            return;
        }

        $this->session->set(
            self::TWO_FACTOR_PENDING_KEY,
            $hashKey
        );
    }

    /**
     * Retourne le hash_key de l'utilisateur
     * en attente de validation 2FA.
     */
    public function getTwoFactorPendingHashKey(): ?string
    {
        $hashKey = $this->session->get(
            self::TWO_FACTOR_PENDING_KEY
        );

        if (
            !is_string($hashKey) ||
            $hashKey === ''
        ) {
            return null;
        }

        return $hashKey;
    }

    /**
     * Supprime l'état d'attente 2FA.
     */
    public function clearTwoFactorPending(): void
    {
        $this->session->remove(
            self::TWO_FACTOR_PENDING_KEY
        );
    }

    /*
     * =========================================================
     * RECOVERY CODES
     * =========================================================
     */

    /**
     * Génère les codes de récupération.
     *
     * Les codes en clair ne sont retournés qu'une seule fois.
     * Seuls leurs hashes sont conservés en BDD.
     */
    public function generateRecoveryCodes(
        int $number = 10
    ): array {
        if (!$this->isLogged()) {
            return [];
        }

        $hashKey = $this->hashKey();

        if ($hashKey === null) {
            return [];
        }

        if ($number <= 0) {
            return [];
        }

        /*
         * Génération des codes.
         */
        $codes = RecoveryCode::generateList(
            $number
        );

        /*
         * Suppression des anciens codes.
         */
        $this->db->table(
            'belcms_user_recovery'
        );

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        $this->db->delete();

        /*
         * Enregistrement des hashes.
         */
        foreach ($codes as $code) {

            $hash = password_hash(
                $code,
                PASSWORD_DEFAULT
            );

            if ($hash === false) {
                continue;
            }

            $this->db->table(
                'belcms_user_recovery'
            );

            $this->db->insert([
                'hash_key'  => $hashKey,
                'code_hash' => $hash,
                'used'      => 0
            ]);
        }

        return $codes;
    }

    /**
     * Retourne le nombre de codes de récupération
     * encore disponibles.
     */
    public function getRecoveryCodeCount(): int
    {
        if (!$this->isLogged()) {
            return 0;
        }

        $hashKey = $this->hashKey();

        if ($hashKey === null) {
            return 0;
        }

        $this->db->table(
            'belcms_user_recovery'
        );

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        $this->db->where([
            'name'  => 'used',
            'value' => 0
        ]);

        return $this->db->count();
    }
    /**
     * Vérifie et consomme un code de récupération.
     *
     * Un code ne peut être utilisé qu'une seule fois.
     */
    public function verifyRecoveryCode(
        string $code
    ): bool {
        $code = trim($code);

        if ($code === '') {
            return false;
        }

        /*
        * Il faut être dans une authentification 2FA
        * en attente.
        */
        if (!$this->isTwoFactorPending()) {
            return false;
        }

        $hashKey = $this->getTwoFactorPendingHashKey();

        if (
            $hashKey === null ||
            $hashKey === ''
        ) {
            return false;
        }

        /*
        * Récupération des codes encore disponibles.
        */
        $this->db->table(
            'belcms_user_recovery'
        );

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        $this->db->where([
            'name'  => 'used',
            'value' => 0
        ]);

        $this->db->queryAll();

        $recoveryCodes = $this->db->data ?? [];

        if (!is_array($recoveryCodes)) {
            return false;
        }

        /*
        * Vérification de chaque hash.
        */
        foreach ($recoveryCodes as $recovery) {

            if (
                !isset($recovery->id) ||
                !isset($recovery->code_hash)
            ) {
                continue;
            }

            if (!password_verify(
                $code,
                $recovery->code_hash
            )) {
                continue;
            }

            /*
            * Le code est valide.
            *
            * On le consomme immédiatement.
            */
            $this->db->table(
                'belcms_user_recovery'
            );

            $this->db->where([
                'name'  => 'id',
                'value' => (int) $recovery->id
            ]);

            $updated = $this->db->update([
                'used'    => 1,
                'used_at' => date('Y-m-d H:i:s')
            ]);

            return (bool) $updated;
        }

        return false;
    }
    /**
     * Vérifie si les codes de récupération
     * peuvent être gérés.
     */
    public function canManageRecoveryCodes(): bool
    {
        return $this->isLogged()
            && $this->isTwoFactorEnabled();
    }
}