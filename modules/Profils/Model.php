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

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

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
     * Retourne le profil par son hash_key.
     */
    public function getByHashKey(
        string $hashKey
    ): mixed {

        $sql = new BDD();

        $sql->table(TABLE_USER_PROFILS);

        $sql->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        $sql->queryOne();

        return $sql->data;
    }

    /**
     * Met à jour le profil utilisateur.
     */
    public function updateProfile(
        string $hashKey,
        array $data
    ): bool {

        if ($hashKey === '' || empty($data)) {
            return false;
        }

        $sql = new BDD();

        $sql->table(TABLE_USER_PROFILS);

        $sql->where([
            'name'  => 'hash_key',
            'value' => $hashKey
        ]);

        return $sql->update($data);
    }

	
}