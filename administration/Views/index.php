<?php

declare(strict_types=1);

/**
 * Bel-CMS V5
 * Tableau de bord administration
 */

$user = $user ?? null;
$accessDenied = $accessDenied ?? false;

$username = 'Administrateur';

if (is_object($user) && isset($user->username)) {
    $username = (string)$user->username;
}

?>

<?php if ($accessDenied): ?>

    <div class="admin-page">

        <div class="admin-card admin-access-denied">

            <div class="admin-access-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <div class="admin-access-content">

                <h1>Accès interdit</h1>

                <p>
                    Vous ne possédez pas les droits nécessaires
                    pour accéder à l'administration de Bel-CMS.
                </p>

                <a href="/"
                   class="admin-button">
                    <i class="fa-solid fa-house"></i>
                    Retour au site
                </a>

            </div>

        </div>

    </div>

<?php else: ?>

    <div class="admin-page">

        <!-- =====================================================
             EN-TÊTE
             ===================================================== -->

        <div class="admin-welcome">

            <div>

                <span class="admin-welcome-label">
                    CENTRE D'ADMINISTRATION
                </span>

                <h1>
                    Bienvenue,
                    <?= htmlspecialchars(
                        $username,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h1>

                <p>
                    Gérez votre installation Bel-CMS depuis
                    cette interface d'administration.
                </p>

            </div>

            <div class="admin-welcome-icon">
                <i class="fa-solid fa-cube"></i>
            </div>

        </div>


        <!-- =====================================================
             BLOCS PRINCIPAUX
             ===================================================== -->

        <div class="admin-dashboard-grid">

            <!-- Utilisateurs -->

            <a href="/admin/users"
               class="admin-dashboard-card">

                <div class="admin-dashboard-icon">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div class="admin-dashboard-content">

                    <h2>
                        Utilisateurs
                    </h2>

                    <p>
                        Gérer les comptes utilisateurs,
                        les profils et les accès.
                    </p>

                    <span class="admin-dashboard-link">
                        Gérer les utilisateurs
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>

                </div>

            </a>


            <!-- Groupes -->

            <a href="/admin/groups"
               class="admin-dashboard-card">

                <div class="admin-dashboard-icon">
                    <i class="fa-solid fa-user-group"></i>
                </div>

                <div class="admin-dashboard-content">

                    <h2>
                        Groupes
                    </h2>

                    <p>
                        Gérer les groupes et les permissions
                        des utilisateurs.
                    </p>

                    <span class="admin-dashboard-link">
                        Gérer les groupes
                        <i class="fa-solid fa-arrow-right"></i>
                    </span>

                </div>

            </a>


            <!-- Modules -->

<!-- Modules -->

<a href="/admin/modules"
   class="admin-dashboard-card">

    <div class="admin-dashboard-icon">
        <i class="fa-solid fa-puzzle-piece"></i>
    </div>

    <div class="admin-dashboard-content">

        <h2>
            Modules
        </h2>

        <p>
            Consulter et gérer les modules
            installés dans Bel-CMS.
        </p>

        <div class="admin-dashboard-stat">

            <span class="admin-stat-value">
                <?= number_format(
                    (int)($totalModules ?? 0),
                    0,
                    ',',
                    ' '
                ) ?>
            </span>

            <span class="admin-stat-label">
                modules installés
            </span>

        </div>

        <span class="admin-dashboard-link">
            Gérer les modules
            <i class="fa-solid fa-arrow-right"></i>
        </span>

    </div>

</a>


            <!-- Sécurité -->

<!-- Sécurité -->

<a href="/admin/security"
   class="admin-dashboard-card">

    <div class="admin-dashboard-icon">
        <i class="fa-solid fa-shield-halved"></i>
    </div>

    <div class="admin-dashboard-content">

        <h2>
            Sécurité
        </h2>

        <p>
            Contrôler les paramètres de sécurité
            et la protection du CMS.
        </p>

        <div class="admin-security-status">

            <?php if (!empty($twoFactorEnabled)): ?>

                <span class="admin-security-status-icon enabled">
                    <i class="fa-solid fa-check"></i>
                </span>

                <span class="admin-security-status-text">
                    Double authentification activée
                </span>

            <?php else: ?>

                <span class="admin-security-status-icon disabled">
                    <i class="fa-solid fa-xmark"></i>
                </span>

                <span class="admin-security-status-text">
                    Double authentification désactivée
                </span>

            <?php endif; ?>

        </div>

        <span class="admin-dashboard-link">
            Centre de sécurité
            <i class="fa-solid fa-arrow-right"></i>
        </span>

    </div>

</a>

        </div>

        <!-- =====================================================
             INFORMATIONS
             ===================================================== -->

<section class="admin-information">

    <div class="admin-section-title">

        <span>
            BEL-CMS V5
        </span>

        <h2>
            Centre d'administration
        </h2>

    </div>


    <div class="admin-info-grid">

        <div class="admin-info-card">

            <div class="admin-info-icon">
                <i class="fa-solid fa-circle-info"></i>
            </div>

            <div>
                <strong>
                    Administration
                </strong>

                <p>
                    Cette interface permet de gérer
                    les différents composants de Bel-CMS.
                </p>
            </div>

        </div>


        <div class="admin-info-card">

            <div class="admin-info-icon">
                <i class="fa-solid fa-code"></i>
            </div>

            <div>
                <strong>
                    Version
                </strong>

                <p>
                    Bel-CMS V5
                </p>
            </div>

        </div>


        <div class="admin-info-card">

            <div class="admin-info-icon">
                <i class="fa-solid fa-server"></i>
            </div>

            <div>
                <strong>
                    Système
                </strong>

                <p>
                    Administration centrale du CMS
                </p>
            </div>

        </div>

    </div>

</section>

</div>
<?php endif; ?>