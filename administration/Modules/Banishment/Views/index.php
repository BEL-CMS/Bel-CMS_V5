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
 
if(!defined('CHECK_INDEX')){http_response_code(403);exit('Accès interdit.');}

$stats=$stats??['total'=>0,'active'=>0,'expired'=>0,'revoked'=>0];$listing=$listing??['items'=>[],'page'=>1,'pages'=>1];$items=$listing['items']??[];$e=static fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');$now = gmdate('Y-m-d H:i:s');

?>
<div class="ban-admin">
    <header class="ban-header">
        <div>
            <span class="ban-eyebrow">SÉCURITÉ • MODÉRATION</span>
            <h1>Gestion des bannissements</h1>
            <p>Consultez et gérez les bannissements du site.</p>
        </div>
        <div class="ban-actions">
            <a class="ban-btn" href="/admin/banishment/logs">Historique</a>
            <a class="ban-btn ban-btn-primary" href="/admin/banishment/create">+ Nouveau bannissement</a>
        </div>
    </header>
    <?php if(!empty($message)):?>
        <div class="ban-alert"><?=$e($message)?></div>
    <?php endif;?>
    <section class="ban-stats">
        <article>
            <span>Total</span><strong><?=(int)$stats['total']?></strong>
        </article>
        <article><span>Actifs</span><strong><?=(int)$stats['active']?></strong></article>
        <article><span>Expirés</span><strong><?=(int)$stats['expired']?></strong></article>
        <article><span>Révoqués</span><strong><?=(int)$stats['revoked']?></strong></article>
    </section>
    <form method="get" action="/admin/banishment" class="ban-filter"><input type="search" name="search" value="<?=$e($search??'')?>" placeholder="Rechercher référence (32 caractères), hash_key, IP ou motif">
        <select name="status">
            <option value="all" <?=($status??'all')==='all'?'selected':''?>>Tous les statuts</option><option value="active" <?=($status??'')==='active'?'selected':''?>>Actifs</option><option value="expired" <?=($status??'')==='expired'?'selected':''?>>Expirés</option>
            <option value="revoked" <?=($status??'')==='revoked'?'selected':''?>>Révoqués</option>
        </select>
        <button class="ban-btn" type="submit">Filtrer</button>
    </form>
    <div class="ban-table-wrap">
        <table class="ban-table">
            <thead>
                <tr>
                    <th>Référence du bannissement</th>
                    <th>Cible</th><th>Motif</th>
                    <th>Dates</th><th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if(!$items):?>
                <tr><td colspan="6" class="ban-empty">Aucun bannissement trouvé.</td></tr>
                <?php else:foreach($items as $r):
                    $rev=!empty($r->revoked_at);$exp=!$rev&&!empty($r->expires_at)&&(string)$r->expires_at<=$now;$label=$rev?'Révoqué':($exp?'Expiré':'Actif');$class=$rev?'revoked':($exp?'expired':'active');?>
                    <tr>
                        <td>
                            <code><?=$e($r->banishment_key??'Référence absente')?></code>
                            <br>
                            <small class="ban-muted">ID interne : #<?=(int)($r->id??0)?></small>
                        </td>
                        <td><?php if(!empty($r->hash_key)):?>
                            <div><b>Compte</b> <code><?=$e($r->hash_key)?></code>
                        </div><?php endif;?><?php if(!empty($r->ip_address)):?><div>
                            <b>IP</b> <code><?=$e($r->ip_address)?></code>
                        </div><?php endif;?></td><td><?=nl2br($e($r->reason??'—'))?></td>
                        <td><small>Créé : <?=$e($r->created_at??'—')?></small><br>
                            <small>Expire : <?=empty($r->expires_at)?'Jamais':$e($r->expires_at)?></small>
                        </td>
                        <td><span class="ban-status <?=$class?>"><?=$label?></span></td>
                        <td><?php if(!$rev&&!$exp):?>
                            <form method="post" action="/admin/banishment/revoke" class="ban-revoke-form" onsubmit="return confirm('Révoquer ce bannissement ?');">
                                <input type="hidden" name="csrf_token" value="<?=$e($csrfToken??'')?>">
                                <input type="hidden" name="id" value="<?=(int)($r->id??0)?>">
                                <input type="text" name="revoke_reason" maxlength="500" placeholder="Motif (facultatif)"><button class="ban-btn ban-btn-danger" type="submit">Révoquer</button></form><?php else:?>
                                    <span class="ban-muted">—</span><?php endif;?>
                        </td>
                    </tr>
                    <?php endforeach;
                endif;?>
            </tbody>
        </table>
    </div>
    <?php if(($listing['pages']??1)>1):?>
        <nav class="ban-pagination" aria-label="Pagination">
            <?php for($p=1;$p<=(int)$listing['pages'];$p++):?>
                <a class="<?=$p===(int)$listing['page']?'current':''?>" href="/admin/banishment?page=<?=$p?>&search=<?=rawurlencode((string)($search??''))?>&status=<?=rawurlencode((string)($status??'all'))?>"><?=$p?></a>
                <?php endfor;?>
        </nav>
        <?php endif;?>
</div>
