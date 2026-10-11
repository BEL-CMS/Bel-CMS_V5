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
?>

<div class="ban-admin">

    <header class="ban-header">
        <div>
            <span class="ban-eyebrow">SÉCURITÉ • MODÉRATION</span><h1>Nouveau bannissement</h1>
            <p>Ciblez un compte, une adresse IP ou les deux.</p>
        </div>
        <a class="ban-btn" href="/admin/banishment">← Retour</a>
    </header>
    <?php if($error!==''):?>
        <div class="ban-alert ban-alert-error"><?=$e($error)?>
        </div>
    <?php endif;?>
    <form method="post" action="/admin/banishment/create" class="ban-form">
        <?= $csrf ?>
        <label>Hash key <span>(facultatif si une IP est fournie)</span>
        <input type="text" name="hash_key" maxlength="64" autocomplete="off" placeholder="Identifiant hash_key">
        </label>
        <label>Adresse IP <span>(facultatif si une hash_key est fournie)</span>
            <input type="text" name="ip_address" maxlength="45" autocomplete="off" placeholder="IPv4 ou IPv6">
        </label>
        <label>Durée
            <select name="level" required><?php foreach($durations as $level=>$label):?>
                <option value="<?=(int)$level?>" <?=(int)$level===9?'selected':''?>><?=$e($label)?></option><?php endforeach;?>
            </select>
        </label>
        <label>Motif
            <textarea name="reason" rows="4" maxlength="500" required placeholder="Motif du bannissement">
        </textarea>
        </label>
        <div class="ban-form-actions">
            <button class="ban-btn ban-btn-primary" type="submit">Créer le bannissement</button>
            <a class="ban-btn" href="/admin/banishment">Annuler</a>
        </div>
    </form>
    <p class="ban-note">Un bannissement par IP peut également toucher d'autres utilisateurs partageant la même adresse.</p>
</div>
