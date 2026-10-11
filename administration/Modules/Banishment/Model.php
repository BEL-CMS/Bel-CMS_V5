<?php
declare(strict_types=1);
namespace BelCMS\Admin\Banishment;
use BelCMS\Core\BDD;

final class Model
{
    public function __construct(private BDD $db) {}

    private function allBanishments(): array
    {
        $this->db->table('TABLE_BANISHMENT');
        $result = $this->db->queryAll();
        $rows = is_array($result) ? $result : ($this->db->data ?? []);
        return is_array($rows) ? array_values(array_filter($rows, 'is_object')) : [];
    }

    public function getStats(): array
    {
        $rows = $this->allBanishments(); $now = gmdate('Y-m-d H:i:s');
        $s = ['total'=>count($rows),'active'=>0,'expired'=>0,'revoked'=>0];
        foreach ($rows as $r) {
            if (!empty($r->revoked_at)) $s['revoked']++;
            elseif (empty($r->expires_at) || (string)$r->expires_at > $now) $s['active']++;
            else $s['expired']++;
        }
        return $s;
    }

    public function getBanishments(int $page=1, int $perPage=25, string $search='', string $status='all'): array
    {
        $page=max(1,$page); $perPage=min(100,max(1,$perPage)); $now=gmdate('Y-m-d H:i:s');
        $search=function_exists('mb_strtolower') ? mb_strtolower(trim($search),'UTF-8') : strtolower(trim($search));
        $rows=array_values(array_filter($this->allBanishments(), static function($r) use($search,$status,$now): bool {
            $hay=function_exists('mb_strtolower') ? mb_strtolower((string)($r->banishment_key??'').' '.(string)($r->hash_key??'').' '.(string)($r->ip_address??'').' '.(string)($r->reason??''),'UTF-8') : strtolower((string)($r->banishment_key??'').' '.(string)($r->hash_key??'').' '.(string)($r->ip_address??'').' '.(string)($r->reason??''));
            if ($search!=='' && !str_contains($hay,$search)) return false;
            $revoked=!empty($r->revoked_at); $expired=!$revoked && !empty($r->expires_at) && (string)$r->expires_at <= $now; $active=!$revoked && !$expired;
            return match($status) {'active'=>$active,'expired'=>$expired,'revoked'=>$revoked,default=>true};
        }));
        usort($rows,static fn($a,$b)=>(int)($b->id??0)<=>(int)($a->id??0));
        $total=count($rows);
        return ['items'=>array_slice($rows,($page-1)*$perPage,$perPage),'total'=>$total,'page'=>$page,'perPage'=>$perPage,'pages'=>max(1,(int)ceil($total/$perPage))];
    }

    public function getLogs(int $limit=100): array
    {
        $this->db->table('TABLE_BANISHMENT_LOGS'); $result=$this->db->queryAll();
        $rows=is_array($result)?$result:($this->db->data??[]); if(!is_array($rows)) return [];
        usort($rows,static fn($a,$b)=>strcmp((string)($b->created_at??''),(string)($a->created_at??'')));
        return array_slice(array_values(array_filter($rows,'is_object')),0,min(500,max(1,$limit)));
    }
}
