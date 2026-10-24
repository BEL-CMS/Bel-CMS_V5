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

namespace BelCMS\Modules\Profils;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

use BelCMS\Core\BDD;

final class Model
{
    /**
     * =========================================================
     * PROFIL
     * =========================================================
     */

    public function getByHashKey(string $hashKey): ?object
    {
        $hashKey = trim($hashKey);

        if ($hashKey === '') {
            return null;
        }

        $sql = new BDD();

        $sql->table(TABLE_USERS_PROFILS);

        $sql->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);

        $sql->queryOne();

        return is_object($sql->data)
            ? $sql->data
            : null;
    }

    /**
     * =========================================================
     * RESEAUX SOCIAUX
     * =========================================================
     */

    public function getSocialByHashKey(string $hashKey): ?object
    {
        $hashKey = trim($hashKey);

        if ($hashKey === '') {
            return null;
        }

        $sql = new BDD();

        $sql->table(TABLE_USERS_SOCIAL);

        $sql->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);

        $sql->queryOne();

        return is_object($sql->data)
            ? $sql->data
            : null;
    }

    /**
     * Met à jour uniquement les champs autorisés du profil personnel.
     * Le hash_key provient de la session, jamais du formulaire.
     */
    public function updateProfile(string $hashKey, array $data): bool
    {
        $hashKey = trim($hashKey);
        if ($hashKey === '') {
            return false;
        }

        $allowed = [
            'last_name', 'gender', 'public_mail', 'websites', 'infos_text',
            'phone', 'birthday', 'country', 'profils', 'gravatar',
        ];
        $values = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $values[$field] = $data[$field];
            }
        }
        if ($values === []) {
            return false;
        }

        $sql = new BDD();
        $sql->table(TABLE_USERS_PROFILS);
        $sql->where(['name' => 'hash_key', 'value' => $hashKey]);
        $updated = $sql->update($values);

        // Le pseudo est stocké dans la table des comptes. On le met à jour séparément.
        if (array_key_exists('username', $data)) {
            $username = trim((string) $data['username']);
            if ($username === '' || mb_strlen($username) < 3 || mb_strlen($username) > 100) {
                return false;
            }
            $account = new BDD();
            $account->table(TABLE_USERS);
            $account->where(['name' => 'hash_key', 'value' => $hashKey]);
            $updated = $account->update(['username' => $username]) && $updated;
        }

        return $updated;
    }

    /**
     * Enregistre les réseaux sociaux.
     */
    public function saveSocial(
        string $hashKey,
        array $data
    ): bool {
        $hashKey = trim($hashKey);

        if ($hashKey === '') {
            return false;
        }

        $fields = [
            'facebook',
            'youtube',
            'whatsapp',
            'instagram',
            'messenger',
            'tiktok',
            'snapchat',
            'telegram',
            'pinterest',
            'x_twitter',
            'reddit',
            'linkedIn',
            'skype',
            'viber',
            'teams_ms',
            'discord',
            'twitch',
        ];

        $values = [];

        foreach ($fields as $field) {
            $values[$field] = trim(
                (string)($data[$field] ?? '')
            );
        }

        /*
         * Vérifie si une ligne existe déjà.
         */
        $existing = $this->getSocialByHashKey($hashKey);

        /*
         * Mise à jour.
         */
        if ($existing !== null) {

            $sql = new BDD();

            $sql->table(TABLE_USERS_SOCIAL);

            $sql->where([
                'name'  => 'hash_key',
                'value' => $hashKey,
            ]);

            return $sql->update($values);
        }

        /*
         * Création.
         */
        $sql = new BDD();

        $sql->table(TABLE_USERS_SOCIAL);

        return $sql->insert(
            array_merge(
                [
                    'hash_key' => $hashKey,
                ],
                $values
            )
        );
    }
}