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

<div class="user-password">
    <div class="user-edit-header">
        <a href="/user" class="user-profile-back">← Retour au centre utilisateur</a>
        <span class="user-profile-label">Sécurité du compte</span>
        <h1>Modifier mon mot de passe</h1>
        <p>Choisissez un nouveau mot de passe pour sécuriser votre compte.</p>
    </div>
    <?php if (!empty($error)): ?>
        <div class="user-message user-message-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="user-message user-message-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    <section class="user-edit-card">
        <div class="user-edit-card-header">
            <span class="user-profile-card-label">Authentification</span>
            <h2>Nouveau mot de passe</h2>
        </div>
        <form method="post" action="/user/password" class="user-edit-form">
            <div class="user-form-group">
                <label for="current_password">Mot de passe actuel</label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password" required>
            </div>
            <div class="user-form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" minlength="8" autocomplete="new-password" required>
                <span class="user-form-help">
                    Minimum 8 caractères.
                </span>
            </div>
            <div class="user-form-group">
                <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="8" autocomplete="new-password" required>
            </div>
            <div class="user-edit-actions">
                <a href="/user/profile" class="user-button user-button-secondary">Annuler</a>
                <button type="submit" class="user-button user-button-primary">Modifier le mot de passe</button>
            </div>
        </form>
    </section>
</div>