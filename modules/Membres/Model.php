<?php
/** Bel-CMS V5 — Module Membres | Apache-2.0 */
declare(strict_types=1);
namespace BelCMS\Modules\Membres;
use BelCMS\Core\BDD;
final class Model
{
    private const PUBLIC_FIELDS = [
        'hash_key', 'username', 'avatar', 'cover_avatar', 'last_name',
        'country', 'infos_text', 'websites', 'profils', 'date_registration', 'public_mail',
    ];

    public function getMembers(int $page = 1, int $perPage = 24, string $search = ''): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(60, $perPage));
        $db = new BDD();
        $db->table(TABLE_USERS_PROFILS);
        $db->fields(['hash_key', 'username', 'avatar', 'country', 'infos_text']);
        if ($search !== '') {
            $db->whereLike(['name' => 'username', 'value' => $search]);
        }
        $db->orderby([['name' => 'username', 'type' => 'ASC']]);
        $db->limit([($page - 1) * $perPage, $perPage], true);
        $rows = $db->queryAll();
        return array_values(array_filter($rows, static fn($member): bool =>
            is_object($member)
            && trim((string) ($member->hash_key ?? '')) !== ''
            && trim((string) ($member->username ?? '')) !== ''
        ));
    }

    public function countMembers(string $search = ''): int
    {
        $db = new BDD();
        $db->table(TABLE_USERS_PROFILS);
        if ($search !== '') {
            $db->whereLike(['name' => 'username', 'value' => $search]);
        }
        return $db->count();
    }

    public function getMemberByHashKey(string $hashKey): ?object
    {
        $hashKey = trim($hashKey);
        if ($hashKey === '') return null;
        $db = new BDD();
        $db->table(TABLE_USERS_PROFILS);
        $db->fields(self::PUBLIC_FIELDS);
        $db->where(['name' => 'hash_key', 'value' => $hashKey, 'op' => '=']);
        $db->queryOne();
        return is_object($db->data) ? $db->data : null;
    }

    public function getSocialByHashKey(string $hashKey): ?object
    {
        $hashKey = trim($hashKey);
        if ($hashKey === '' || !defined('TABLE_USERS_SOCIAL')) return null;
        $db = new BDD();
        $db->table(TABLE_USERS_SOCIAL);
        $db->fields([
            'hash_key', 'facebook', 'youtube', 'whatsapp', 'instagram', 'messenger',
            'tiktok', 'snapchat', 'telegram', 'pinterest', 'x_twitter', 'reddit',
            'linkedIn', 'skype', 'viber', 'teams_ms', 'discord', 'twitch',
        ]);
        $db->where(['name' => 'hash_key', 'value' => $hashKey, 'op' => '=']);
        $db->queryOne();
        return is_object($db->data) ? $db->data : null;
    }
}
