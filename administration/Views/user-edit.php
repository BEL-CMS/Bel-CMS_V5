<?php

declare(strict_types=1);

$editUser = $editUser ?? null;
$errors   = $errors ?? [];
$success  = $success ?? null;

?>

<div class="admin-page">

    <div class="admin-page-header">

        <div>

            <span class="admin-welcome-label">
                ADMINISTRATION
            </span>

            <h1>
                Modifier un utilisateur
            </h1>

            <p>
                Gestion du compte et des droits utilisateur.
            </p>

        </div>

        <div class="admin-page-header-icon">
            <i class="fa-solid fa-user-gear"></i>
        </div>

    </div>


    <?php if (!empty($errors)): ?>

        <div class="admin-user-alert admin-user-alert-danger">

            <i class="fa-solid fa-circle-exclamation"></i>

            <div>

                <?php foreach ($errors as $error): ?>

                    <div>
                        <?= htmlspecialchars(
                            (string)$error,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>

                <?php endforeach; ?>

            </div>

        </div>

    <?php endif; ?>


    <?php if ($success !== null): ?>

        <div class="admin-user-alert admin-user-alert-success">

            <i class="fa-solid fa-circle-check"></i>

            <div>

                <?= htmlspecialchars(
                    (string)$success,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>

    <?php endif; ?>


    <?php if (is_object($editUser)): ?>

        <form
            action="/admin/users/edit?hash_key=<?= urlencode(
                (string)$editUser->hash_key
            ) ?>"
            method="post"
            class="admin-user-edit-form"
        >

            <?= csrf_field(); ?>

            <input
                type="hidden"
                name="hash_key"
                value="<?= htmlspecialchars(
                    (string)$editUser->hash_key,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >


            <div class="admin-card admin-user-edit-card">

                <div class="admin-user-edit-heading">

                    <div class="admin-user-edit-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div>

                        <span>
                            COMPTE UTILISATEUR
                        </span>

                        <h2>
                            <?= htmlspecialchars(
                                (string)($editUser->username ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                    </div>

                </div>


                <div class="admin-user-form-grid">

                    <div class="admin-user-form-field">

                        <label for="username">
                            Nom d'utilisateur
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            maxlength="100"
                            required
                            value="<?= htmlspecialchars(
                                (string)($editUser->username ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                    </div>


                    <div class="admin-user-form-field">

                        <label for="email">
                            Adresse e-mail
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            maxlength="255"
                            required
                            value="<?= htmlspecialchars(
                                (string)($editUser->email ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >

                    </div>

                </div>

<div class="admin-form-section">
    <h3>
        <i class="fa-solid fa-key"></i>
        Sécurité
    </h3>

    <div class="admin-form-grid">

        <div class="admin-form-group">
            <label for="new_password">
                Nouveau mot de passe
            </label>

            <input
                type="password"
                id="new_password"
                name="new_password"
                class="admin-form-input"
                autocomplete="new-password"
                placeholder="Laisser vide pour ne pas modifier"
            >

            <small class="admin-form-help">
                Minimum 8 caractères.
            </small>
        </div>

        <div class="admin-form-group">
            <label for="new_password_confirm">
                Confirmation du mot de passe
            </label>

            <input
                type="password"
                id="new_password_confirm"
                name="new_password_confirm"
                class="admin-form-input"
                autocomplete="new-password"
                placeholder="Confirmer le nouveau mot de passe"
            >
        </div>

    </div>
</div>

<a
    href="/admin/users/sessions?hash_key=<?= urlencode(
        (string)$user->hash_key
    ) ?>"
    class="admin-button"
>
    <i class="fa-solid fa-laptop"></i>
    Voir les sessions
</a>

<div class="admin-form-section">

    <h3>
        <i class="fa-solid fa-shield-halved"></i>
        Authentification à deux facteurs
    </h3>

    <div class="admin-security-row">

        <div class="admin-security-info">

            <div class="admin-security-title">
                État du 2FA
            </div>

            <?php if (!empty($user->two_factor_enabled) && !empty($user->two_factor_secret)): ?>

                <span class="admin-status admin-status-success">
                    <i class="fa-solid fa-circle-check"></i>
                    Activé
                </span>

                <p class="admin-form-help">
                    L'utilisateur utilise actuellement
                    l'authentification à deux facteurs.
                </p>

            <?php else: ?>

                <span class="admin-status admin-status-warning">
                    <i class="fa-solid fa-circle-xmark"></i>
                    Désactivé
                </span>

                <p class="admin-form-help">
                    Aucun système 2FA n'est actuellement actif.
                </p>

            <?php endif; ?>

        </div>

        <?php if (!empty($user->two_factor_enabled) && !empty($user->two_factor_secret)): ?>

            <form
                action="/admin/users/2fa/reset"
                method="post"
                onsubmit="return confirm(
                    'Voulez-vous vraiment réinitialiser le 2FA de cet utilisateur ?\n\nLes codes de récupération actuels seront également supprimés.'
                );"
            >

                <?= csrf_field(); ?>

                <input
                    type="hidden"
                    name="hash_key"
                    value="<?= htmlspecialchars(
                        (string)$user->hash_key,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                <button
                    type="submit"
                    class="admin-button admin-button-danger"
                >
                    <i class="fa-solid fa-rotate"></i>
                    Réinitialiser le 2FA
                </button>

            </form>

        <?php endif; ?>

    </div>

</div>



                <div class="admin-user-edit-separator"></div>


                <div class="admin-user-permissions">

                    <h3>
                        <i class="fa-solid fa-shield-halved"></i>
                        Droits du compte
                    </h3>


                    <label class="admin-user-switch">

                        <input
                            type="checkbox"
                            name="valid"
                            value="1"
                            <?= !empty($editUser->valid)
                                ? 'checked'
                                : ''
                            ?>
                        >

                        <span class="admin-user-switch-slider"></span>

                        <span class="admin-user-switch-content">

                            <strong>
                                Compte actif
                            </strong>

                            <small>
                                Autorise l'utilisateur à se connecter.
                            </small>

                        </span>

                    </label>


                    <label class="admin-user-switch">

                        <input
                            type="checkbox"
                            name="admin"
                            value="1"
                            <?= (
                                !empty($editUser->admin)
                                || !empty($editUser->is_admin)
                            )
                                ? 'checked'
                                : ''
                            ?>
                        >

                        <span class="admin-user-switch-slider"></span>

                        <span class="admin-user-switch-content">

                            <strong>
                                Administrateur
                            </strong>

                            <small>
                                Autorise l'accès au centre d'administration.
                            </small>

                        </span>

                    </label>

                </div>


                <div class="admin-user-edit-footer">

                    <a
                        href="/admin/users"
                        class="admin-button admin-button-secondary"
                    >
                        <i class="fa-solid fa-arrow-left"></i>
                        Retour
                    </a>

                    <button
                        type="submit"
                        class="admin-button"
                    >
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer
                    </button>

                </div>

            </div>

        </form>

    <?php endif; ?>

</div>