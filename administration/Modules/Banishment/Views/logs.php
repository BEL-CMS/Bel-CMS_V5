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

use BelCMS\Core\User;

?>

<div class="ban-admin">
    <header class="ban-header">
        <div>
            <span class="ban-eyebrow">SÉCURITÉ • AUDIT</span>
            <h1>Historique des bannissements</h1>
            <p>Actions récemment enregistrées.</p>
        </div>
        <a class="ban-btn" href="/admin/banishment">← Retour</a>
    </header>
    <div class="ban-table-wrap">
        <table class="ban-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>Cible</th>
                    <th>Par administrateur</th>
                    <th>Motif</th>
                    <th>ID</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!$logs):?>
                <tr>
                    <td colspan="6" class="ban-empty">Aucune entrée.</td>
                </tr><?php else:foreach($logs as $r):?><tr>
                    <td><?=($r->created_at??'—')?></td>
                    <td><span class="ban-status"><?=($r->action??'—')?></span></td>
                    <td><?php if(!empty($r->target_hash_key)):?>
                    <div>
                        <code><?=($r->target_hash_key)?></code>
                    </div>
                    <?php endif;?><?php if(!empty($r->target_ip)):?>
                    <div>
                        <code><?=($r->target_ip)?></code>
                    </div><?php endif;?>
                    </td>
                    <td>
                        <code><?=(User::isUserToHahskey($r->admin_hash_key??'—') ? User::isUserToHahskey($r->admin_hash_key??'—') : '—')?></code>
                    </td>
                    <td>
                        <?=nl2br(($r->reason??'—'))?>
                    </td>
                    <td><?=($r->banishment_id??'—')?></td>
                </tr><?php endforeach;endif;?>
            </tbody>
        </table>
    </div>
</div>
