<?php

declare(strict_types=1);

$statistics = $statistics ?? [
    'users' => 0,
    'profiles' => 0,
    'groups' => 0,
    'sessions' => 0,
];

$system = $system ?? [
    'php' => PHP_VERSION,
    'cms' => '5.0.0',
    'database' => 'MySQL',
];

?>

<div class="admin-dashboard">

    <!-- En-tête -->
    <div class="admin-page-header">

        <div>
            <h1>
                <i class="fa-solid fa-gauge-high"></i>
                Dashboard
            </h1>

            <p>
                Vue d'ensemble de votre administration Bel-CMS.
            </p>
        </div>

    </div>


    <!-- Statistiques -->
    <div class="dashboard-stats">

        <!-- Utilisateurs -->
        <div class="dashboard-card">

            <div class="dashboard-card-icon">
                <i class="fa-solid fa-users"></i>
            </div>

            <div class="dashboard-card-content">

                <span class="dashboard-card-label">
                    Utilisateurs
                </span>

                <strong class="dashboard-card-value">
                    <?= number_format(
                        (int) $statistics['users'],
                        0,
                        ',',
                        ' '
                    ) ?>
                </strong>

            </div>

            <div class="dashboard-card-link">

                <a href="/admin/users">
                    Gérer les utilisateurs
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </div>

        </div>


        <!-- Profils -->
        <div class="dashboard-card">

            <div class="dashboard-card-icon">
                <i class="fa-solid fa-id-card"></i>
            </div>

            <div class="dashboard-card-content">

                <span class="dashboard-card-label">
                    Profils
                </span>

                <strong class="dashboard-card-value">
                    <?= number_format(
                        (int) $statistics['profiles'],
                        0,
                        ',',
                        ' '
                    ) ?>
                </strong>

            </div>

            <div class="dashboard-card-link">

                <a href="/admin/users">
                    Voir les profils
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </div>

        </div>


        <!-- Groupes -->
        <div class="dashboard-card">

            <div class="dashboard-card-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>

            <div class="dashboard-card-content">

                <span class="dashboard-card-label">
                    Groupes
                </span>

                <strong class="dashboard-card-value">
                    <?= number_format(
                        (int) $statistics['groups'],
                        0,
                        ',',
                        ' '
                    ) ?>
                </strong>

            </div>

            <div class="dashboard-card-link">

                <a href="/admin/groups">
                    Gérer les groupes
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </div>

        </div>


        <!-- Sessions -->
        <div class="dashboard-card">

            <div class="dashboard-card-icon">
                <i class="fa-solid fa-plug"></i>
            </div>

            <div class="dashboard-card-content">

                <span class="dashboard-card-label">
                    Sessions
                </span>

                <strong class="dashboard-card-value">
                    <?= number_format(
                        (int) $statistics['sessions'],
                        0,
                        ',',
                        ' '
                    ) ?>
                </strong>

            </div>

            <div class="dashboard-card-link">

                <a href="/admin/users">
                    Gestion des sessions
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </div>

        </div>

    </div>


    <!-- Deux colonnes -->
    <div class="dashboard-columns">


        <!-- Actions rapides -->
        <section class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div class="dashboard-panel-icon">
                    <i class="fa-solid fa-bolt"></i>
                </div>

                <div>
                    <h2>
                        Actions rapides
                    </h2>

                    <p>
                        Accédez rapidement aux fonctions principales.
                    </p>
                </div>

            </div>


            <div class="dashboard-quick-actions">

                <a
                    href="/admin/users"
                    class="dashboard-quick-action"
                >

                    <span class="quick-action-icon">
                        <i class="fa-solid fa-users"></i>
                    </span>

                    <span>
                        <strong>
                            Utilisateurs
                        </strong>

                        <small>
                            Gérer les comptes
                        </small>
                    </span>

                    <i class="fa-solid fa-chevron-right"></i>

                </a>


                <a
                    href="/admin/groups"
                    class="dashboard-quick-action"
                >

                    <span class="quick-action-icon">
                        <i class="fa-solid fa-layer-group"></i>
                    </span>

                    <span>
                        <strong>
                            Groupes
                        </strong>

                        <small>
                            Gérer les groupes
                        </small>
                    </span>

                    <i class="fa-solid fa-chevron-right"></i>

                </a>


                <a
                    href="/admin/modules"
                    class="dashboard-quick-action"
                >

                    <span class="quick-action-icon">
                        <i class="fa-solid fa-puzzle-piece"></i>
                    </span>

                    <span>
                        <strong>
                            Modules
                        </strong>

                        <small>
                            Gérer les modules
                        </small>
                    </span>

                    <i class="fa-solid fa-chevron-right"></i>

                </a>


                <a
                    href="/admin/security"
                    class="dashboard-quick-action"
                >

                    <span class="quick-action-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>

                    <span>
                        <strong>
                            Sécurité
                        </strong>

                        <small>
                            Centre de sécurité
                        </small>
                    </span>

                    <i class="fa-solid fa-chevron-right"></i>

                </a>

            </div>

        </section>


        <!-- État du système -->
        <section class="dashboard-panel">

            <div class="dashboard-panel-header">

                <div class="dashboard-panel-icon">
                    <i class="fa-solid fa-server"></i>
                </div>

                <div>
                    <h2>
                        État du système
                    </h2>

                    <p>
                        Informations de votre installation.
                    </p>
                </div>

            </div>


            <div class="dashboard-system-list">


                <!-- PHP -->
                <div class="dashboard-system-item">

                    <div class="system-item-name">

                        <span class="system-item-icon">
                            <i class="fa-brands fa-php"></i>
                        </span>

                        <span>
                            PHP
                        </span>

                    </div>

                    <div class="system-item-value">

                        <strong>
                            <?= htmlspecialchars(
                                (string) $system['php'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <span class="system-status">
                            <i class="fa-solid fa-circle"></i>
                            OK
                        </span>

                    </div>

                </div>


                <!-- MySQL -->
                <div class="dashboard-system-item">

                    <div class="system-item-name">

                        <span class="system-item-icon">
                            <i class="fa-solid fa-database"></i>
                        </span>

                        <span>
                            Base de données
                        </span>

                    </div>

                    <div class="system-item-value">

                        <strong>
                            <?= htmlspecialchars(
                                (string) $system['database'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <span class="system-status">
                            <i class="fa-solid fa-circle"></i>
                            OK
                        </span>

                    </div>

                </div>


                <!-- Bel-CMS -->
                <div class="dashboard-system-item">

                    <div class="system-item-name">

                        <span class="system-item-icon">
                            <i class="fa-solid fa-cubes"></i>
                        </span>

                        <span>
                            Bel-CMS
                        </span>

                    </div>

                    <div class="system-item-value">

                        <strong>
                            <?= htmlspecialchars(
                                (string) $system['cms'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                        <span class="system-status">
                            <i class="fa-solid fa-circle"></i>
                            OK
                        </span>

                    </div>

                </div>


            </div>

        </section>

    </div>


    <!-- Bienvenue -->
    <section class="dashboard-welcome">

        <div class="dashboard-welcome-icon">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <div>

            <h2>
                Administration Bel-CMS V5
            </h2>

            <p>
                Bienvenue dans votre espace d'administration.
                Depuis cette interface, vous pouvez gérer les utilisateurs,
                les membres, les groupes et les différents modules de votre CMS.
            </p>

        </div>

    </section>

</div>