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
    
}