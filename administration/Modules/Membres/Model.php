<?php
/** Bel-CMS V5 — Administration des membres | Apache-2.0 */
declare(strict_types=1);
namespace BelCMS\Admin\Membres;
use BelCMS\Core\BDD;
final class Model
{
    public function __construct(private BDD $db) {}

    public function getMembers(int $page = 1, int $perPage = 25, string $search = ''): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $this->db->table(TABLE_USERS);
        $this->db->fields(['hash_key', 'username', 'valid']);
        if ($search !== '') $this->db->whereLike(['name' => 'username', 'value' => $search]);
        $this->db->orderby('DESC');
        $this->db->limit([($page - 1) * $perPage, $perPage], true);
        return $this->db->queryAll();
    }

    public function countMembers(string $search = ''): int
    {
        $this->db->table(TABLE_USERS);
        if ($search !== '') $this->db->whereLike(['name' => 'username', 'value' => $search]);
        return $this->db->count();
    }
    public function getMemberForEdit(string $hashKey): ?array
    {
        $hashKey = trim($hashKey);
        if ($hashKey === '') return null;

        $this->db->table(TABLE_USERS);
        $this->db->fields(['hash_key', 'username', 'email', 'valid']);
        $this->db->where(['name' => 'hash_key', 'value' => $hashKey]);
        $this->db->queryOne();
        $account = is_object($this->db->data) ? $this->db->data : null;
        if ($account === null) return null;

        $this->db->table(TABLE_USERS_PROFILS);
        $this->db->fields([
            'hash_key', 'last_name', 'gender', 'public_mail', 'websites',
            'infos_text', 'country', 'profils', 'gravatar', 'avatar', 'cover_avatar'
        ]);
        $this->db->where(['name' => 'hash_key', 'value' => $hashKey]);
        $this->db->queryOne();
        $profile = is_object($this->db->data) ? $this->db->data : null;

        return ['account' => $account, 'profile' => $profile];
    }

    public function updateMember(string $hashKey, array $accountData, array $profileData): bool
    {
        $hashKey = trim($hashKey);
        if ($hashKey === '') return false;

        $accountAllowed = ['username', 'email', 'valid'];
        $accountValues = array_intersect_key($accountData, array_flip($accountAllowed));
        $profileAllowed = [
            'last_name', 'gender', 'public_mail', 'websites', 'infos_text',
            'country', 'profils', 'gravatar', 'avatar', 'cover_avatar'
        ];
        $profileValues = array_intersect_key($profileData, array_flip($profileAllowed));

        if (isset($accountValues['username'])) {
            $accountValues['username'] = trim((string) $accountValues['username']);
            if (mb_strlen($accountValues['username']) < 3 || mb_strlen($accountValues['username']) > 100) return false;
        }
        if (isset($accountValues['email']) && !filter_var($accountValues['email'], FILTER_VALIDATE_EMAIL)) return false;
        if (isset($profileValues['public_mail']) && $profileValues['public_mail'] !== '' && !filter_var($profileValues['public_mail'], FILTER_VALIDATE_EMAIL)) return false;
        if (!empty($profileValues['websites']) && (!filter_var($profileValues['websites'], FILTER_VALIDATE_URL) || !preg_match('~^https?://~i', $profileValues['websites']))) return false;
        // Avatars and covers may be local paths in Bel-CMS, or absolute HTTP(S) URLs.
        foreach (['avatar', 'cover_avatar', 'gravatar'] as $mediaField) {
            $media = trim((string) ($profileValues[$mediaField] ?? ''));
            if ($media === '') continue;
            $isHttpUrl = filter_var($media, FILTER_VALIDATE_URL) && preg_match('~^https?://~i', $media);
            $isLocalPath = preg_match('~^(?:/[A-Za-z0-9_./-]+|[A-Za-z0-9_./-]+)$~', $media) === 1 && !str_contains($media, '..');
            if (!$isHttpUrl && !$isLocalPath) return false;
        }

        $ok = true;
        if ($accountValues !== []) {
            $this->db->table(TABLE_USERS);
            $this->db->where(['name' => 'hash_key', 'value' => $hashKey]);
            $ok = $this->db->update($accountValues) && $ok;
        }
        if ($profileValues !== []) {
            $this->db->table(TABLE_USERS_PROFILS);
            $this->db->where(['name' => 'hash_key', 'value' => $hashKey]);
            $ok = $this->db->update($profileValues) && $ok;
        }
        return $ok;
    }

}
