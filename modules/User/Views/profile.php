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

<div class="user-profile">
    <div class="user-profile-header">
        <a href="/user" class="user-profile-back">← Retour au centre utilisateur</a>
        <div class="user-profile-heading">
            <div class="user-profile-avatar">
                <?= strtoupper(
                    mb_substr(
                        $user->username ?? '?',
                        0,
                        1
                    )
                ) ?>
            </div>
            <div>
                <span class="user-profile-label">Mon profil</span>
                <h1>
                    <?= htmlspecialchars(
                        $user->username ?? 'Utilisateur'
                    ) ?>
                </h1>
                <p>
                    <?= htmlspecialchars(
                        $user->email ?? ''
                    ) ?>
                </p>
            </div>
        </div>
    </div>
    <div class="user-profile-grid">
        <section class="user-profile-card">
            <div class="user-profile-card-header">
                <div>
                    <span class="user-profile-card-label">Informations</span>
                    <h2>Mon compte</h2>
                </div>
                <a href="/user/edit">Modifier</a>
            </div>
            <div class="user-profile-fields">
                <div class="user-profile-field">
                    <span>Nom d'utilisateur</span>
                    <strong>
                        <?= htmlspecialchars(
                            $user->username ?? ''
                        ) ?>
                    </strong>
                </div>
                <div class="user-profile-field">
                    <span>Adresse email</span>
                    <strong>
                        <?= htmlspecialchars(
                            $user->email ?? ''
                        ) ?>
                    </strong>
                </div>
                <div class="user-profile-field">
                    <span>Identifiant du compte</span>
                    <strong>#<?= (int) ($user->id ?? 0) ?></strong>
                </div>
                <div class="user-profile-field">
                    <span>État du compte</span>
                    <strong>
                        <?php if (!empty($user->valid)): ?>
                            <span class="user-status user-status-valid">
                                Compte validé
                            </span>
                        <?php else: ?>
                            <span class="user-status user-status-warning">
                                Compte non validé
                            </span>
                        <?php endif; ?>
                    </strong>
                </div>
            </div>
        </section>
        <section class="user-profile-card">
            <div class="user-profile-card-header">
                <div>
                    <span class="user-profile-card-label">Sécurité</span>
                    <h2>Protection du compte</h2>
                </div>
                <a href="/user/security">Gérer</a>
            </div>
            <div class="user-security-status">
                <div class="user-security-icon">🔐</div>
                <div>
                    <strong>Authentification à deux facteurs</strong>
                    <p>
                        <?php if (!empty(
                            $user->two_factor_enabled
                        )): ?>

                            L'authentification 2FA est activée.

                        <?php else: ?>

                            L'authentification 2FA est actuellement désactivée.

                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="user-profile-security-link">
                <a href="/user/password">Modifier mon mot de passe →</a>
            </div>
        </section>
    </div>
    <section class="user-profile-card user-profile-session">
        <div class="user-profile-card-header">
            <div>
                <span class="user-profile-card-label">Session actuelle</span>
                <h2>Connexion</h2>
            </div>
            <span class="user-status user-status-valid">Connecté</span>
        </div>
        <div class="user-profile-session-info">
            <div>
                <span>Identifiant utilisateur</span>
                <strong>#
                <?= htmlspecialchars(
                    (string)($user->hash_key ?? '-'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
                </strong>
            </div>
            <div>
                <span>Adresse IP</span>
                <strong>
                <?= htmlspecialchars(
                    (string)($user->ip ?? '-'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
                </strong>
            </div>
        </div>
    </section>
</div>