<?php

declare(strict_types=1);

$statistics = $statistics ?? [
    'users'    => 0,
    'profiles' => 0,
    'groups'   => 0,
    'sessions' => 0,
];

?>

<div class="admin-dashboard">

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
                    <?= (int) $statistics['users'] ?>
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
                    <?= (int) $statistics['profiles'] ?>
                </strong>
            </div>

            <div class="dashboard-card-link">
                <a href="/admin/membres">
                    Voir les membres
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
                    <?= (int) $statistics['groups'] ?>
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
                    <?= (int) $statistics['sessions'] ?>
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


    <!-- Bienvenue -->
    <div class="dashboard-welcome">

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

    </div>

</div>