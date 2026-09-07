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

/**
 * @var object $user
 * @var int $recoveryCount
 */
?>

<div class="user-page">

<div class="user-page-header">
    <div>
        <h1>
            <i class="fa-solid fa-shield-halved"></i>
            Sécurité
        </h1>

        <p>
            Gérez les paramètres de sécurité de votre compte.
        </p>
    </div>

    <a href="/user" class="user-btn user-btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        Retour
    </a>
</div>


<!-- =====================================================
     Authentification à deux facteurs
     ===================================================== -->

<section class="user-security-card">

    <div class="user-security-card-header">

        <div class="user-security-card-icon">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <div>
            <h3>Authentification à deux facteurs</h3>

            <p>
                Renforcez la sécurité de votre compte avec une authentification supplémentaire.
            </p>
        </div>

    </div>


    <div class="user-security-card-content">

        <?php if (!empty($user->two_factor_enabled)): ?>

            <div class="user-security-status user-security-status-success">
                <i class="fa-solid fa-circle-check"></i>
                Authentification à deux facteurs activée
            </div>

            <a
                href="/user/security/2fa"
                class="user-security-link"
            >
                <span>
                    <strong>Gérer la double authentification</strong>

                    <small>
                        Modifier ou désactiver la protection 2FA
                    </small>
                </span>

                <i class="fa-solid fa-chevron-right"></i>
            </a>

        <?php else: ?>

            <div class="user-security-status user-security-status-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Authentification à deux facteurs désactivée
            </div>

            <a
                href="/user/security/2fa"
                class="user-security-link"
            >
                <span>
                    <strong>Activer la double authentification</strong>

                    <small>
                        Protéger votre compte avec Google Authenticator
                    </small>
                </span>

                <i class="fa-solid fa-chevron-right"></i>
            </a>

        <?php endif; ?>

    </div>

</section>


<!-- =====================================================
     Codes de récupération
     ===================================================== -->

<section class="user-security-card">

    <div class="user-security-card-header">

        <div class="user-security-card-icon">
            <i class="fa-solid fa-key"></i>
        </div>

        <div>
            <h3>Codes de récupération</h3>

            <p>
                Utilisez un code de récupération si vous ne pouvez plus accéder à votre application d'authentification.
            </p>
        </div>

    </div>


    <div class="user-security-card-content">

        <div class="user-security-status user-security-status-info">

            <i class="fa-solid fa-circle-info"></i>

            <?php if ((int)$recoveryCount > 0): ?>

                <?= (int)$recoveryCount ?>
                code(s) de récupération disponible(s)

            <?php else: ?>

                Aucun code de récupération disponible

            <?php endif; ?>

        </div>


        <a
            href="/user/security/recovery"
            class="user-security-link"
        >
            <span>

                <strong>
                    Gérer les codes de récupération
                </strong>

                <small>
                    Générer 10 nouveaux codes de récupération
                </small>

            </span>

            <i class="fa-solid fa-chevron-right"></i>

        </a>

    </div>

</section>


<!-- =====================================================
     Sessions actives
     ===================================================== -->

<section class="user-security-card">

    <div class="user-security-card-header">

        <div class="user-security-card-icon">
            <i class="fa-solid fa-laptop"></i>
        </div>

        <div>
            <h3>Sessions actives</h3>

            <p>
                Gérez les appareils actuellement connectés à votre compte.
            </p>
        </div>

    </div>


    <div class="user-security-card-content">

        <p class="user-security-description">
            Consultez les sessions ouvertes sur votre compte et déconnectez les appareils que vous ne reconnaissez pas.
        </p>


        <a
            href="/user/security/sessions"
            class="user-security-link"
        >
            <span>

                <strong>
                    Voir les sessions
                </strong>

                <small>
                    Afficher les appareils connectés
                </small>

            </span>

            <i class="fa-solid fa-chevron-right"></i>

        </a>

    </div>

</section>


<!-- =====================================================
     Mot de passe
     ===================================================== -->

<section class="user-security-card">

    <div class="user-security-card-header">

        <div class="user-security-card-icon">
            <i class="fa-solid fa-lock"></i>
        </div>

        <div>
            <h3>Mot de passe</h3>

            <p>
                Modifiez régulièrement votre mot de passe pour protéger votre compte.
            </p>
        </div>

    </div>


    <div class="user-security-card-content">

        <a
            href="/user/password"
            class="user-security-link"
        >
            <span>

                <strong>
                    Modifier mon mot de passe
                </strong>

                <small>
                    Choisir un nouveau mot de passe
                </small>

            </span>

            <i class="fa-solid fa-chevron-right"></i>

        </a>

    </div>

</section>

</div>
