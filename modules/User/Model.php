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

namespace BelCMS\Modules\User;
use BelCMS\Core\BDD;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

final class Model
{
    /**
     * Retourne un utilisateur par son ID.
     */
    public function getById(int $id): mixed
    {
        $sql = new BDD();

        $sql->table('belcms_user');

        $sql->where([
            'name'  => 'id',
            'value' => $id
        ]);

        $sql->queryOne();

        return $sql->data;
    }

    /**
     * Retourne un utilisateur par son username.
     */
    public function getByUsername(string $username): mixed
    {
        $sql = new BDD();

        $sql->table('belcms_user');

        $sql->where([
            'name'  => 'username',
            'value' => $username
        ]);

        $sql->queryOne();

        return $sql->data;
    }

    /**
     * Retourne un utilisateur par son email.
     */
    public function getByEmail(string $email): mixed
    {
        $sql = new BDD();

        $sql->table('belcms_user');

        $sql->where([
            'name'  => 'email',
            'value' => $email
        ]);

        $sql->queryOne();

        return $sql->data;
    }

    /**
     * Modifie le username.
     */
    public function updateUsername(
        int $userId,
        string $username
    ): bool {
        $username = trim($username);

        if ($username === '') {
            return false;
        }

        $sql = new BDD();

        $sql->table('belcms_user');

        $sql->where([
            'name'  => 'id',
            'value' => $userId
        ]);

        return $sql->update([
            'username' => $username
        ]);
    }

    /**
     * Modifie l'adresse email.
     */
    public function updateEmail(
        int $userId,
        string $email
    ): bool {
        $email = trim($email);

        if ($email === '') {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $sql = new BDD();

        $sql->table('belcms_user');

        $sql->where([
            'name'  => 'id',
            'value' => $userId
        ]);

        return $sql->update([
            'email' => $email
        ]);
    }
    /**
     * Modifie l'IP de l'utilisateur.
     */
    public function updateIp(
        int $userId,
        ?string $ip
    ): bool {
        $sql = new BDD();

        $sql->table('belcms_user');

        $sql->where([
            'name'  => 'id',
            'value' => $userId
        ]);

        return $sql->update([
            'ip' => $ip
        ]);
    }

    /**
     * Modifie plusieurs informations
     * de l'utilisateur en une seule requête.
     */
    public function update(
        int $userId,
        array $data
    ): bool {
        if ($userId <= 0 || empty($data)) {
            return false;
        }

        $allowed = [
            'username',
            'email',
            'ip',
        ];

        $values = [];

        foreach ($data as $field => $value) {

            if (!in_array($field, $allowed, true)) {
                continue;
            }

            if ($field === 'username') {
                $value = trim((string) $value);

                if ($value === '') {
                    continue;
                }
            }

            if ($field === 'email') {
                $value = trim((string) $value);

                if (
                    $value === '' ||
                    !filter_var(
                        $value,
                        FILTER_VALIDATE_EMAIL
                    )
                ) {
                    continue;
                }
            }

            $values[$field] = $value;
        }

        if (empty($values)) {
            return false;
        }

        $sql = new BDD();

        $sql->table('belcms_user');

        $sql->where([
            'name'  => 'id',
            'value' => $userId
        ]);

        return $sql->update($values);
    }

/**
 * Vérifie le mot de passe actuel.
 */
public function verifyPassword(
    int $userId,
    string $password
): bool {
    $user = $this->getById($userId);

    if (!$user || !isset($user->password)) {
        return false;
    }

    return password_verify(
        $password,
        $user->password
    );
}

/**
 * Modifie le mot de passe.
 */
    public function updatePassword(
        int $userId,
        string $password
    ): bool {
        if ($userId <= 0 || $password === '') {
            return false;
        }

        $hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        if ($hash === false) {
            return false;
        }

        $sql = new BDD();

        $sql->table('belcms_user');

        $sql->where([
            'name'  => 'id',
            'value' => $userId
        ]);

        return $sql->update([
            'password' => $hash
        ]);
    }

    /**
     * Retourne un utilisateur par son hash_key.
     */
    public function getByHashKey(
        string $hashKey
    ): mixed {
        $sql = new BDD();

        $sql->table('belcms_user');

        $sql->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        $sql->queryOne();

        return $sql->data;
    }
/*
 * Supprime une inscription temporaire.
 */
public function deleteTemporary(
    string $validationKey
): bool {
    $validationKey = trim($validationKey);

    if ($validationKey === '') {
        return false;
    }

    $sql = new BDD();

    $sql->table('belcms_user_temp');

    $sql->where([
        'name'  => 'validation_key',
        'value' => $validationKey
    ]);

    return $sql->delete();
}


/**
 * Crée un utilisateur définitif
 * à partir d'une inscription temporaire.
 */
public function createFromTemporary(
    object $temporaryUser
): bool {
    if (
        empty($temporaryUser->username) ||
        empty($temporaryUser->hash_key) ||
        empty($temporaryUser->email) ||
        empty($temporaryUser->password)
    ) {
        return false;
    }

    /*
     * Vérification de l'expiration.
     */
    if (
        !empty($temporaryUser->expires_at)
        && strtotime((string)$temporaryUser->expires_at) <= time()
    ) {
        return false;
    }

    /*
     * Vérifie que le compte définitif
     * n'existe pas déjà.
     */
    if (
        $this->getByUsername(
            (string)$temporaryUser->username
        )
    ) {
        return false;
    }

    if (
        $this->getByEmail(
            (string)$temporaryUser->email
        )
    ) {
        return false;
    }

    /*
     * Création du compte définitif.
     */
    $sql = new BDD();

    $sql->table('belcms_user');

    return $sql->insert([
        'username'           => $temporaryUser->username,
        'hash_key'           => $temporaryUser->hash_key,
        'password'           => $temporaryUser->password,
        'email'              => $temporaryUser->email,
        'ip'                 => $temporaryUser->ip,
        'valid'              => 1,
        'two_factor_enabled' => 0,
        'two_factor_secret'  => null,
    ]);
}
    /**
     * Enregistre une nouvelle session utilisateur.
     */
    public function createSession(
        string $hashKey,
        string $sessionToken,
        string $ip,
        string $userAgent,
        string $createdAt,
        string $lastActivity,
        string $expiresAt
    ): bool {
        if (
            $hashKey === '' ||
            $sessionToken === ''
        ) {
            return false;
        }

        $sql = new BDD();

        $sql->table('belcms_user_sessions');

        return $sql->insert([
            'hash_key'       => $hashKey,
            'session_token'  => $sessionToken,
            'ip'             => $ip,
            'user_agent'     => $userAgent,
            'created_at'     => $createdAt,
            'last_activity'  => $lastActivity,
            'expires_at'     => $expiresAt,
        ]);
    }
    /**
     * Retourne une session utilisateur par son token.
     */
    public function getSessionByToken(
        string $sessionToken
    ): mixed {
        $sql = new BDD();

        $sql->table('belcms_user_sessions');

        $sql->where([
            'name'  => 'session_token',
            'value' => $sessionToken
        ]);

        $sql->queryOne();

        return $sql->data;
    }
    /**
     * Supprime une session par son token.
     */
    public function deleteSessionByToken(
        string $sessionToken
    ): bool {
        $sql = new BDD();

        $sql->table('belcms_user_sessions');

        $sql->where([
            'name'  => 'session_token',
            'value' => $sessionToken
        ]);

        return $sql->delete();
    }
    /**
     * Crée un nouvel utilisateur.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): bool
    {
        /*
        * Données obligatoires
        */
        $username = trim((string)($data['username'] ?? ''));
        $email    = trim((string)($data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if (
            $username === '' ||
            $email === '' ||
            $password === ''
        ) {
            return false;
        }

        /*
        * Validation du username
        */
        if (
            mb_strlen($username) < 3 ||
            mb_strlen($username) > 100
        ) {
            return false;
        }

        /*
        * Validation de l'adresse email
        */
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        /*
        * Vérifie qu'un username
        * n'existe pas déjà.
        */
        if ($this->getByUsername($username)) {
            return false;
        }

        /*
        * Vérifie qu'un email
        * n'existe pas déjà.
        */
        if ($this->getByEmail($email)) {
            return false;
        }

        /*
        * Création du hash utilisateur.
        *
        * 32 caractères hexadécimaux.
        */
        $hashKey = bin2hex(random_bytes(16));

        /*
        * Hash sécurisé du mot de passe.
        */
        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        if ($passwordHash === false) {
            return false;
        }

        /*
        * IP de création du compte.
        */
        $ip = $data['ip'] ?? null;

        if (
            $ip !== null &&
            !is_string($ip)
        ) {
            $ip = null;
        }

        /*
        * Création du compte.
        *
        * valid = 1
        * pour le moment.
        *
        * Nous pourrons ensuite passer
        * à une validation par email.
        */
        $sql = new BDD();

        $sql->table('belcms_user');

        return $sql->insert([
            'username'           => $username,
            'hash_key'           => $hashKey,
            'password'           => $passwordHash,
            'email'              => $email,
            'ip'                 => $ip,
            'valid'              => 1,
            'two_factor_enabled' => 0,
            'two_factor_secret'  => null,
        ]);
    }
    public function createTemporary(
        string $username,
        string $hashKey,
        string $email,
        string $passwordHash,
        ?string $ip,
        string $validationKey,
        string $expiresAt
    ): bool {
        $username      = trim($username);
        $hashKey       = trim($hashKey);
        $email         = trim($email);
        $passwordHash  = trim($passwordHash);
        $validationKey = trim($validationKey);
        $expiresAt     = trim($expiresAt);

        if (
            $username === '' ||
            $hashKey === '' ||
            $email === '' ||
            $passwordHash === '' ||
            $validationKey === '' ||
            $expiresAt === ''
        ) {
            return false;
        }

        $sql = new BDD();

        $sql->table('belcms_user_temp');

        return $sql->insert([
            'username'       => $username,
            'hash_key'       => $hashKey,
            'email'          => $email,
            'password'       => $passwordHash,
            'ip'             => $ip,
            'validation_key' => $validationKey,
            'expires_at'     => $expiresAt,
        ]);
    }
    /**
     * Retourne une inscription temporaire
     * grâce à sa clé de validation.
     */
    public function getTemporaryByValidationKey(
        string $validationKey
    ): mixed {
        $validationKey = trim($validationKey);

        if ($validationKey === '') {
            return null;
        }

        $sql = new BDD();

        $sql->table('belcms_user_temp');

        $sql->where([
            'name'  => 'validation_key',
            'value' => $validationKey
        ]);

        $sql->queryOne();

        return $sql->data;
    }
    /**
     * Retourne une inscription temporaire
     * par username.
     */
    public function getTemporaryByUsername(
        string $username
    ): mixed {
        $username = trim($username);

        if ($username === '') {
            return null;
        }

        $sql = new BDD();

        $sql->table('belcms_user_temp');

        $sql->where([
            'name'  => 'username',
            'value' => $username
        ]);

        $sql->queryOne();

        return $sql->data;
    }
    /**
     * Retourne une inscription temporaire
     * par email.
     */
    public function getTemporaryByEmail(
        string $email
    ): mixed {
        $email = trim($email);

        if ($email === '') {
            return null;
        }

        $sql = new BDD();

        $sql->table('belcms_user_temp');

        $sql->where([
            'name'  => 'email',
            'value' => $email
        ]);

        $sql->queryOne();

        return $sql->data;
    } 

    public function cleanupTemporary(): bool
    {
        $sql = new BDD();

        $sql->table('belcms_user_temp');

        $sql->where([
            'name'  => 'expires_at',
            'value' => date('Y-m-d H:i:s')
        ]);

        return $sql->delete();
    }
}