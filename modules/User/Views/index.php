<?php

declare(strict_types=1);
?>

<div class="user-dashboard">

    <div class="user-dashboard-header">

        <div>
            <span class="user-dashboard-label">
                Centre utilisateur
            </span>

            <h1>
                Bienvenue
                <?= htmlspecialchars(
                    $user->username ?? 'Utilisateur'
                ) ?>
            </h1>

            <p>
                Gérez votre compte et vos paramètres
                depuis cet espace.
            </p>
        </div>

        <div class="user-dashboard-avatar">
            <?= strtoupper(
                mb_substr(
                    $user->username ?? '?',
                    0,
                    1
                )
            ) ?>
        </div>

    </div>


    <div class="user-dashboard-grid">


        <!-- Profil -->

        <a
            href="/user/profile"
            class="user-card"
        >

            <div class="user-card-icon">
                👤
            </div>

            <div class="user-card-content">

                <h2>
                    Mon profil
                </h2>

                <p>
                    Consultez vos informations personnelles.
                </p>

            </div>

            <span class="user-card-arrow">
                →
            </span>

        </a>


        <!-- Modifier -->

        <a
            href="/user/edit"
            class="user-card"
        >

            <div class="user-card-icon">
                ✏️
            </div>

            <div class="user-card-content">

                <h2>
                    Modifier mon profil
                </h2>

                <p>
                    Modifiez votre nom d'utilisateur
                    et votre adresse email.
                </p>

            </div>

            <span class="user-card-arrow">
                →
            </span>

        </a>


        <!-- Sécurité -->

        <a
            href="/user/security"
            class="user-card"
        >

            <div class="user-card-icon">
                🔐
            </div>

            <div class="user-card-content">

                <h2>
                    Sécurité
                </h2>

                <p>

                    <?php if (
                        !empty($user->two_factor_enabled)
                    ): ?>

                        Authentification 2FA activée.

                    <?php else: ?>

                        Consultez les paramètres
                        de sécurité de votre compte.

                    <?php endif; ?>

                </p>

            </div>

            <span class="user-card-arrow">
                →
            </span>

        </a>


        <!-- Mot de passe -->

        <a
            href="/user/password"
            class="user-card"
        >

            <div class="user-card-icon">
                🔑
            </div>

            <div class="user-card-content">

                <h2>
                    Mot de passe
                </h2>

                <p>
                    Modifiez votre mot de passe.
                </p>

            </div>

            <span class="user-card-arrow">
                →
            </span>

        </a>


        <!-- Sessions -->

        <a
            href="/user/sessions"
            class="user-card"
        >

            <div class="user-card-icon">
                💻
            </div>

            <div class="user-card-content">

                <h2>
                    Mes sessions
                </h2>

                <p>
                    Gérez vos connexions et vos appareils.
                </p>

            </div>

            <span class="user-card-arrow">
                →
            </span>

        </a>


        <!-- Déconnexion -->

        <a
            href="/user/logout"
            class="user-card user-card-danger"
        >

            <div class="user-card-icon">
                🚪
            </div>

            <div class="user-card-content">

                <h2>
                    Déconnexion
                </h2>

                <p>
                    Déconnectez-vous de votre compte.
                </p>

            </div>

            <span class="user-card-arrow">
                →
            </span>

        </a>


    </div>


    <div class="user-dashboard-info">

        <div>
            <span>
                Identifiant
            </span>

            <strong>
                #<?= (int) $user->id ?>
            </strong>
        </div>

        <div>
            <span>
                Adresse email
            </span>

            <strong>
                <?= htmlspecialchars(
                    $user->email ?? ''
                ) ?>
            </strong>
        </div>

        <div>
            <span>
                Compte
            </span>

            <strong>

                <?php if (!empty($user->valid)): ?>

                    Validé

                <?php else: ?>

                    Non validé

                <?php endif; ?>

            </strong>
        </div>

    </div>

</div>