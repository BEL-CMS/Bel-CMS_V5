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
<div class="user-edit">
    <div class="user-edit-header">
        <a href="/user" class="user-profile-back">← Retour au centre utilisateur</a>
        <span class="user-profile-label">Mon compte</span>
        <h1>Modifier mon profil</h1>
        <p>Modifiez les informations principales de votre compte.</p>
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
            <div>
                <span class="user-profile-card-label">
                    Informations personnelles
                </span>
                <h2>Mon profil</h2>
            </div>
        </div>
        <form method="post" action="/user/edit" class="user-edit-form">
            <div class="user-form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" maxlength="100" autocomplete="username" required>
                <span class="user-form-help">Entre 3 et 100 caractères.</span>
            </div>
            <div class="user-form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" maxlength="255" autocomplete="email" required>
            </div>
            <div class="user-edit-actions">
                <a href="/user/profile" class="user-button user-button-secondary">Annuler</a>
                <button type="submit" class="user-button user-button-primary">Enregistrer les modifications</button>
            </div>
        </form>
    </section>
</div>