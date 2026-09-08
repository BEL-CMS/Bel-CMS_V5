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

<div class="user-recovery">
    <div class="user-edit-header">
        <a href="/user/security" class="user-profile-back">← Retour à la sécurité</a>
        <span class="user-profile-label">Sécurité</span>
        <h1>Codes de récupération</h1>
        <p>Utilisez un code de récupération si vous ne pouvez pas accéder à votre application d'authentification.</p>
    </div>
    <?php if (!empty($success)): ?>
        <div class="user-message user-message-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="user-message user-message-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($recoveryCodes)): ?>
        <!--
            Les codes viennent d'être générés.
            Ils ne seront plus récupérables ensuite.
        -->
        <section class="user-security-card">
            <div class="user-2fa-introduction">
                <span class="user-profile-card-label">Nouveaux codes</span>
                <h2>Conservez-les précieusement</h2>
                <p>Ces codes sont affichés une seule fois. Copiez-les ou imprimez-les avant de quitter cette page.
                </p>
            </div>
            <div class="user-recovery-warning">
                <strong>⚠️ Attention</strong>
                <p>La génération de nouveaux codes invalide immédiatement tous les anciens codes.</p>
            </div>
            <div class="user-recovery-codes">
                <?php foreach (
                    $recoveryCodes as $recoveryCode
                ): ?>
                    <code><?= htmlspecialchars($recoveryCode) ?></code>
                <?php endforeach; ?>
            </div>
            <div class="user-security-action">
                <div>
                    <strong>Codes enregistrés</strong>
                    <p>Les codes affichés ci-dessus sont maintenant enregistrés sous forme de hash sécurisé.
                    </p>
                </div>
                <a href="/user/security" class="user-button user-button-primary">J'ai sauvegardé mes codes</a>
            </div>
        </section>
    <?php else: ?>
        <!--
            État normal
        -->
        <section class="user-security-card">
            <div class="user-security-card-header">
                <div>
                    <span class="user-profile-card-label">État actuel</span>
                    <h2>Vos codes de récupération</h2>
                </div>
                <span class="user-status user-status-valid"><?= (int) $count ?> disponible(s)</span>
            </div>
            <div class="user-security-action">
                <div>
                    <strong>Générer de nouveaux codes</strong>
                    <p>Un nouveau jeu de 10 codes remplacera immédiatement les codes actuellement enregistrés.</p>
                </div>
                <form method="post" action="/user/security/recovery">
                    <button type="submit" class="user-button user-button-primary">Générer 10 nouveaux codes</button>
                </form>
            </div>
        </section>
    <?php endif; ?>
    <div class="user-security-footer">
        <a href="/user/security">← Retour à la sécurité</a>
    </div>
</div>