<?php

declare(strict_types=1);
?>

<div class="user-security">

    <div class="user-edit-header">

        <a
            href="/user"
            class="user-profile-back"
        >
            ← Retour au centre utilisateur
        </a>

        <span class="user-profile-label">
            Sécurité
        </span>

        <h1>
            Sécurité du compte
        </h1>

        <p>
            Gérez les protections et les paramètres de sécurité
            de votre compte.
        </p>

    </div>


    <!-- État général -->

    <section class="user-security-card">

        <div class="user-security-card-header">

            <div>

                <span class="user-profile-card-label">
                    État du compte
                </span>

                <h2>
                    Protection générale
                </h2>

            </div>

            <span class="user-status user-status-valid">
                Compte connecté
            </span>

        </div>


        <div class="user-security-grid">

            <div class="user-security-item">

                <div class="user-security-item-icon">
                    ✓
                </div>

                <div>

                    <strong>
                        Compte
                    </strong>

                    <span>
                        <?php if (!empty($user->valid)): ?>
                            Compte validé
                        <?php else: ?>
                            Compte non validé
                        <?php endif; ?>
                    </span>

                </div>

            </div>


            <div class="user-security-item">

                <div class="user-security-item-icon">
                    🔑
                </div>

                <div>

                    <strong>
                        Mot de passe
                    </strong>

                    <span>
                        Protégé par un mot de passe sécurisé
                    </span>

                </div>

            </div>

        </div>

    </section>


    <!-- 2FA -->

    <section class="user-security-card">

        <div class="user-security-card-header">

            <div>

                <span class="user-profile-card-label">
                    Authentification
                </span>

                <h2>
                    Authentification à deux facteurs
                </h2>

            </div>

            <?php if (
                !empty($user->two_factor_enabled)
            ): ?>

                <span class="user-status user-status-valid">
                    Activée
                </span>

            <?php else: ?>

                <span class="user-status user-status-warning">
                    Désactivée
                </span>

            <?php endif; ?>

        </div>


        <div class="user-security-2fa">

            <div class="user-security-item-icon">
                🔐
            </div>

            <div class="user-security-2fa-content">

                <strong>
                    Protection supplémentaire
                </strong>

                <p>
                    L'authentification à deux facteurs ajoute
                    une protection supplémentaire lors de votre
                    connexion.
                </p>

                <?php if (
                    !empty($user->two_factor_enabled)
                ): ?>

                    <a
                        href="#"
                        class="user-button user-button-secondary"
                    >
                        Gérer le 2FA
                    </a>

                <?php else: ?>

                    <a
                        href="/user/security/2fa"
                        class="user-button user-button-primary"
                    >
                        Activer le 2FA
                    </a>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- Mot de passe -->

    <section class="user-security-card">

        <div class="user-security-card-header">

            <div>

                <span class="user-profile-card-label">
                    Mot de passe
                </span>

                <h2>
                    Protection du compte
                </h2>

            </div>

        </div>


        <div class="user-security-action">

            <div>

                <strong>
                    Modifier votre mot de passe
                </strong>

                <p>
                    Utilisez un mot de passe unique et suffisamment
                    robuste pour protéger votre compte.
                </p>

            </div>

            <a
                href="/user/password"
                class="user-button user-button-secondary"
            >
                Modifier
            </a>

        </div>

    </section>

<section class="user-security-card">

    <div class="user-security-card-header">

        <div>

            <span class="user-profile-card-label">
                Récupération
            </span>

            <h2>
                Codes de récupération
            </h2>

        </div>

        <span class="user-status user-status-valid">
            <?= (int) $recoveryCount ?> disponible(s)
        </span>

    </div>


    <div class="user-security-action">

        <div>

            <strong>
                Codes de secours
            </strong>

            <p>
                Utilisez ces codes lorsque votre application
                d'authentification n'est pas disponible.
            </p>

        </div>

        <a
            href="/user/security/recovery"
            class="user-button user-button-secondary"
        >
            Gérer mes codes
        </a>

    </div>

</section>


    <!-- Retour -->

    <div class="user-security-footer">

        <a href="/user">
            ← Retour au centre utilisateur
        </a>

    </div>

</div>