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

use BelCMS\Core\Security\RecoveryCode;
use BelCMS\Core\Security\TOTP\ProvisioningUri;
use BelCMS\Core\Security\TOTP\TOTP;

final class User
{
    private BDD $db;
    private Session $session;
    private UserSession $userSession;

    private ?object $currentUser = null;
    private bool $loaded = false;

    private const SESSION_KEY = 'BELCMS_USER_HASH_KEY';
    private const TWO_FACTOR_PENDING_KEY = 'BELCMS_2FA_PENDING';
    private const TWO_FACTOR_SETUP_KEY = 'BELCMS_2FA_SETUP_SECRET';

    public function __construct(
        BDD $db,
        Session $session,
        UserSession $userSession
    ) {
        $this->db = $db;
        $this->session = $session;
        $this->userSession = $userSession;
    }

    /**
     * Return the currently authenticated user.
     */
    public function current(): ?object
    {
        if ($this->loaded) {
            return $this->currentUser;
        }

        $this->loaded = true;
        $this->currentUser = null;

        $hashKey = $this->session->get(self::SESSION_KEY);

        if (!is_string($hashKey) || trim($hashKey) === '') {
            return null;
        }

        $hashKey = trim($hashKey);

        $this->db->table('belcms_user');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);
        $this->db->queryOne();

        if (is_object($this->db->data)) {
            $this->currentUser = $this->db->data;
        }

        return $this->currentUser;
    }

    public function isLogged(): bool
    {
        return $this->current() !== null;
    }

    /**
     * Technical database ID.
     */
    public function id(): ?int
    {
        $user = $this->current();

        if (!$user || !isset($user->id)) {
            return null;
        }

        return (int)$user->id;
    }

    /**
     * Business/user identifier.
     */
    public function hashKey(): ?string
    {
        $user = $this->current();

        if (!$user || !isset($user->hash_key)) {
            return null;
        }

        return (string)$user->hash_key;
    }

    public function username(): ?string
    {
        $user = $this->current();

        if (!$user || !isset($user->username)) {
            return null;
        }

        return (string)$user->username;
    }

    public function email(): ?string
    {
        $user = $this->current();

        if (!$user || !isset($user->email)) {
            return null;
        }

        return (string)$user->email;
    }

    public function valid(): bool
    {
        $user = $this->current();

        return $user !== null && !empty($user->valid);
    }

    public function ip(): ?string
    {
        $user = $this->current();

        if (!$user || !isset($user->ip)) {
            return null;
        }

        return $user->ip !== null ? (string)$user->ip : null;
    }

    public function token(): ?string
    {
        $user = $this->current();

        if (!$user || !isset($user->token)) {
            return null;
        }

        return $user->token !== null ? (string)$user->token : null;
    }

    public function data(): ?object
    {
        return $this->current();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $user = $this->current();

        if (!$user || !property_exists($user, $key)) {
            return $default;
        }

        return $user->{$key};
    }

    public function reload(): ?object
    {
        $this->loaded = false;
        $this->currentUser = null;

        return $this->current();
    }

    public function isAdmin(): bool
    {
        $user = $this->current();

        if (!$user) {
            return false;
        }

        if (isset($user->admin)) {
            return (bool)$user->admin;
        }

        if (isset($user->is_admin)) {
            return (bool)$user->is_admin;
        }

        return false;
    }

    public function isRoot(): bool
    {
        $user = $this->current();

        if (!$user) {
            return false;
        }

        if (isset($user->root)) {
            return (bool)$user->root;
        }

        if (isset($user->is_root)) {
            return (bool)$user->is_root;
        }

        return false;
    }

    /**
     * Authenticate with email or username.
     *
     * Returns true when the first authentication step succeeds.
     * When 2FA is enabled, a pending 2FA session is created instead
     * of logging the user in directly.
     */
    public function login(string $identifier, string $password): bool
    {
        $identifier = trim($identifier);

        if ($identifier === '' || $password === '') {
            return false;
        }

        $user = $this->findForLogin($identifier);

        if (!$user) {
            return false;
        }

        if (empty($user->valid)) {
            return false;
        }

        if (!isset($user->password) || !password_verify($password, (string)$user->password)) {
            return false;
        }

        if (!isset($user->hash_key) || trim((string)$user->hash_key) === '') {
            return false;
        }

        $hashKey = trim((string)$user->hash_key);

        $this->session->regenerate(true);

        if (!empty($user->two_factor_enabled) && !empty($user->two_factor_secret)) {
            $this->setTwoFactorPending($hashKey);
            $this->clearTwoFactorSetupSecret();

            $this->loaded = false;
            $this->currentUser = null;

            return true;
        }

        $this->session->set(self::SESSION_KEY, $hashKey);
        $this->clearTwoFactorPending();
        $this->clearTwoFactorSetupSecret();

        $this->loaded = false;
        $this->currentUser = null;
        $this->current();

        $this->registerCurrentUserSession();

        return true;
    }

    private function findForLogin(string $identifier): ?object
    {
        $user = null;

        $this->db->table('belcms_user');
        $this->db->where([
            'name'  => 'email',
            'value' => $identifier,
        ]);
        $this->db->queryOne();

        if (is_object($this->db->data)) {
            $user = $this->db->data;
        }

        if ($user !== null) {
            return $user;
        }

        $this->db->table('belcms_user');
        $this->db->where([
            'name'  => 'username',
            'value' => $identifier,
        ]);
        $this->db->queryOne();

        return is_object($this->db->data) ? $this->db->data : null;
    }

    public function completeTwoFactorLogin(): bool
    {
        if (!$this->isTwoFactorPending()) {
            return false;
        }

        $hashKey = $this->getTwoFactorPendingHashKey();

        if ($hashKey === null || $hashKey === '') {
            return false;
        }

        $this->db->table('belcms_user');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);
        $this->db->queryOne();

        $user = $this->db->data;

        if (!is_object($user) || empty($user->valid)) {
            $this->clearTwoFactorPending();
            return false;
        }

        if (empty($user->two_factor_enabled) || empty($user->two_factor_secret)) {
            $this->clearTwoFactorPending();
            return false;
        }

        $this->session->regenerate(true);
        $this->session->set(self::SESSION_KEY, $hashKey);
        $this->clearTwoFactorPending();
        $this->clearTwoFactorSetupSecret();

        $this->loaded = false;
        $this->currentUser = null;
        $this->current();

        $this->registerCurrentUserSession();

        return $this->current() !== null;
    }

    public function logout(): void
    {
        $sessionId = session_id();

        if ($sessionId !== '') {
            $this->userSession->delete($sessionId);
        }

        $this->session->remove(self::SESSION_KEY);
        $this->clearTwoFactorPending();
        $this->clearTwoFactorSetupSecret();

        $this->loaded = false;
        $this->currentUser = null;
    }

    private function registerCurrentUserSession(): void
    {
        $hashKey = $this->hashKey();

        if ($hashKey === null || $hashKey === '') {
            return;
        }

        $this->userSession->current($hashKey);
    }

    public function isTwoFactorEnabled(): bool
    {
        $user = $this->current();

        return $user !== null && !empty($user->two_factor_enabled) && !empty($user->two_factor_secret);
    }

    public function getTwoFactorSecret(): ?string
    {
        $user = $this->current();

        if (!$user || !isset($user->two_factor_secret)) {
            return null;
        }

        $secret = trim((string)$user->two_factor_secret);

        return $secret !== '' ? $secret : null;
    }

    public function generateTwoFactorSecret(): string
    {
        $secret = TOTP::generateSecret();

        $this->session->set(self::TWO_FACTOR_SETUP_KEY, $secret);

        return $secret;
    }

    public function getTwoFactorSetupSecret(): ?string
    {
        $secret = $this->session->get(self::TWO_FACTOR_SETUP_KEY);

        if (!is_string($secret)) {
            return null;
        }

        $secret = trim($secret);

        return $secret !== '' ? $secret : null;
    }

    public function clearTwoFactorSetupSecret(): void
    {
        $this->session->remove(self::TWO_FACTOR_SETUP_KEY);
    }

    public function getTwoFactorUri(?string $secret = null): ?string
    {
        if ($secret === null || trim($secret) === '') {
            $secret = $this->getTwoFactorSecret();
        }

        if ($secret === null || $secret === '') {
            return null;
        }

        $account = $this->email() ?? $this->username() ?? 'user';
        $issuer = 'Bel-CMS';

        return ProvisioningUri::create(
            $secret,
            $account,
            $issuer
        );
    }

    public function verifyTwoFactorCode(string $secret, string $code): bool
    {
        $secret = trim($secret);
        $code = trim($code);

        if ($secret === '' || !preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        return TOTP::verify($secret, $code);
    }

    public function enableTwoFactor(string $secret, string $code): bool
    {
        if (!$this->isLogged()) {
            return false;
        }

        $secret = trim($secret);
        $code = trim($code);

        if ($secret === '' || !$this->verifyTwoFactorCode($secret, $code)) {
            return false;
        }

        $hashKey = $this->hashKey();

        if ($hashKey === null || $hashKey === '') {
            return false;
        }

        $this->db->table('belcms_user');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);

        $updated = $this->db->update([
            'two_factor_enabled' => 1,
            'two_factor_secret'  => $secret,
        ]);

        if (!$updated) {
            return false;
        }

        $this->clearTwoFactorSetupSecret();
        $this->reload();

        return $this->isTwoFactorEnabled();
    }

    public function disableTwoFactor(): bool
    {
        if (!$this->isLogged()) {
            return false;
        }

        $hashKey = $this->hashKey();

        if ($hashKey === null || $hashKey === '') {
            return false;
        }

        $this->db->table('belcms_user');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);

        $updated = $this->db->update([
            'two_factor_enabled' => 0,
            'two_factor_secret'  => null,
        ]);

        if (!$updated) {
            return false;
        }

        $this->clearTwoFactorSetupSecret();
        $this->reload();

        return !$this->isTwoFactorEnabled();
    }

    public function isTwoFactorPending(): bool
    {
        $hashKey = $this->session->get(self::TWO_FACTOR_PENDING_KEY);

        return is_string($hashKey) && trim($hashKey) !== '';
    }

    public function setTwoFactorPending(string $hashKey): void
    {
        $hashKey = trim($hashKey);

        if ($hashKey === '') {
            $this->clearTwoFactorPending();
            return;
        }

        $this->session->set(self::TWO_FACTOR_PENDING_KEY, $hashKey);
        $this->session->remove(self::SESSION_KEY);

        $this->loaded = false;
        $this->currentUser = null;
    }

    public function getTwoFactorPendingHashKey(): ?string
    {
        $hashKey = $this->session->get(self::TWO_FACTOR_PENDING_KEY);

        if (!is_string($hashKey)) {
            return null;
        }

        $hashKey = trim($hashKey);

        return $hashKey !== '' ? $hashKey : null;
    }

    public function clearTwoFactorPending(): void
    {
        $this->session->remove(self::TWO_FACTOR_PENDING_KEY);
    }

    public function canManageRecoveryCodes(): bool
    {
        return $this->isLogged() && $this->isTwoFactorEnabled();
    }

    public function generateRecoveryCodes(int $number = 10): array
    {
        if (!$this->isLogged()) {
            return [];
        }

        if (!$this->isTwoFactorEnabled()) {
            return [];
        }

        $number = max(1, min(50, $number));
        $hashKey = $this->hashKey();

        if ($hashKey === null || $hashKey === '') {
            return [];
        }

        $codes = RecoveryCode::generateList($number);

        if (count($codes) !== $number) {
            return [];
        }

        $this->db->table('belcms_user_recovery');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);
        $this->db->delete();

        $inserted = 0;

        foreach ($codes as $code) {
            $this->db->table('belcms_user_recovery');

            $ok = $this->db->insert([
                'hash_key'   => $hashKey,
                'code_hash'  => password_hash($code, PASSWORD_DEFAULT),
                'used'       => 0,
            ]);

            if ($ok) {
                $inserted++;
            }
        }

        if ($inserted !== $number) {
            return [];
        }

        return $codes;
    }

    public function getRecoveryCodeCount(): int
    {
        if (!$this->isLogged()) {
            return 0;
        }

        $hashKey = $this->hashKey();

        if ($hashKey === null || $hashKey === '') {
            return 0;
        }

        $this->db->table('belcms_user_recovery');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);
        $this->db->where([
            'name'  => 'used',
            'value' => 0,
        ]);
        $this->db->queryAll();

        return is_array($this->db->data) ? count($this->db->data) : 0;
    }

    public function verifyRecoveryCode(string $code): bool
    {
        $code = trim($code);

        if ($code === '' || !$this->isTwoFactorPending()) {
            return false;
        }

        $hashKey = $this->getTwoFactorPendingHashKey();

        if ($hashKey === null || $hashKey === '') {
            return false;
        }

        $this->db->table('belcms_user_recovery');
        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);
        $this->db->where([
            'name'  => 'used',
            'value' => 0,
        ]);
        $this->db->queryAll();

        $recoveryCodes = $this->db->data;

        if (!is_array($recoveryCodes)) {
            return false;
        }

        foreach ($recoveryCodes as $recovery) {
            if (!is_object($recovery)) {
                continue;
            }

            if (!isset($recovery->id, $recovery->code_hash)) {
                continue;
            }

            if (!password_verify($code, (string)$recovery->code_hash)) {
                continue;
            }

            $this->db->table('belcms_user_recovery');
            $this->db->where([
                'name'  => 'id',
                'value' => (int)$recovery->id,
            ]);

            return (bool)$this->db->update([
                'used'    => 1,
                'used_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return false;
    }

    public function verifyPassword(string $password): bool
    {
        if (!$this->isLogged()) {
            return false;
        }

        $user = $this->current();

        if (!$user || !isset($user->password) || (string)$user->password === '') {
            return false;
        }

        return password_verify($password, (string)$user->password);
    }
}
