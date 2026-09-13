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

namespace BelCMS\Modules\Profils;

use BelCMS\Core\BDD;

if (!defined('CHECK_INDEX')):
    header(
        $_SERVER['SERVER_PROTOCOL']
        . ' 403 Direct access forbidden'
    );

    exit(
        '<!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>BEL-CMS : Error 403 Forbidden</title>
        </head>
        <body>
            <h1>HTTP Error 403 : Forbidden</h1>
            <p>You don\'t permission to access / on this server.</p>
        </body>
        </html>'
    );
endif;

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

        $sql->table(TABLE_USER_PROFILS);

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

        $sql->table(TABLE_USER_SOCIAL);

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

            $sql->table(TABLE_USER_SOCIAL);

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

        $sql->table(TABLE_USER_SOCIAL);

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