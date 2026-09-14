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

namespace BelCMS\Modules\Membres;

use BelCMS\Core\BDD;

final class Model
{
    /**
     * Retourne les profils publics disponibles.
     *
     * On utilise la table des profils comme source publique afin de ne jamais
     * exposer directement les champs sensibles de la table principale des utilisateurs.
     */
    public function getMembers(): array
    {
        $db = new BDD();
        $db->table(TABLE_USER_PROFILS);
        $db->queryAll();

        if (!is_array($db->data)) {
            return [];
        }

        return array_values(array_filter(
            $db->data,
            static function ($member): bool {
                return is_object($member)
                    && trim((string) ($member->hash_key ?? '')) !== ''
                    && trim((string) ($member->username ?? '')) !== '';
            }
        ));
    }

    public function getMemberByHashKey(string $hashKey): ?object
    {
        $hashKey = trim($hashKey);

        if ($hashKey === '') {
            return null;
        }

        $db = new BDD();
        $db->table(TABLE_USER_PROFILS);
        $db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);
        $db->queryOne();

        return is_object($db->data) ? $db->data : null;
    }

    public function getSocialByHashKey(string $hashKey): ?object
    {
        $hashKey = trim($hashKey);

        if ($hashKey === '') {
            return null;
        }

        $db = new BDD();
        $db->table(TABLE_USER_SOCIAL);
        $db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);
        $db->queryOne();

        return is_object($db->data) ? $db->data : null;
    }
}
