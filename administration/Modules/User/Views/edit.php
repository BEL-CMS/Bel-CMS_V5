<?php

declare(strict_types=1);

$targetUser = $targetUser ?? null;

$saved = isset($_GET['saved']);
$twoFactorReset = isset($_GET['2fa_reset']);

if (!is_object($targetUser)) {
    exit('Utilisateur introuvable.');
}

$hashKey = (string) ($targetUser->hash_key ?? '');
$username = (string) ($targetUser->username ?? '');
$email = (string) ($targetUser->email ?? '');
$valid = (int) ($targetUser->valid ?? 0);
$twoFactor = (int) ($targetUser->two_factor_enabled ?? 0);

?>

<div class="admin-user-edit">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <div class="admin-user-header">

        <div>
            <h1>
                <i class="fa-solid fa-user-pen"></i>
                Modifier l'utilisateur
            </h1>

            <p>
                Modification du compte
                <strong><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></strong>
            </p>
        </div>

        <a href="/admin/users" class="admin-user-button secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Retour aux utilisateurs
        </a>

    </div>


    <!-- =========================================================
         MESSAGES
    ========================================================== -->

    <?php if ($saved): ?>

        <div class="admin-alert success">
            <i class="fa-solid fa-circle-check"></i>
            Les informations de l'utilisateur ont été enregistrées.
        </div>

    <?php endif; ?>


    <?php if ($twoFactorReset): ?>

        <div class="admin-alert success">
            <i class="fa-solid fa-shield-check"></i>
            L'authentification à deux facteurs a été réinitialisée.
        </div>

    <?php endif; ?>


    <!-- =========================================================
         FORMULAIRE UTILISATEUR
    ========================================================== -->

    <form
        method="post"
        action="/admin/users/edit?hash_key=<?= urlencode($hashKey) ?>"
        class="admin-user-form"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>"
        >

        <input
            type="hidden"
            name="hash_key"
            value="<?= htmlspecialchars($hashKey, ENT_QUOTES, 'UTF-8') ?>"
        >


        <!-- =====================================================
             INFORMATIONS DU COMPTE
        ====================================================== -->

        <div class="admin-user-card">

            <div class="admin-user-card-header">

                <h2>
                    <i class="fa-solid fa-user"></i>
                    Informations du compte
                </h2>

            </div>


            <div class="admin-user-card-body">

                <!-- Username -->

                <div class="admin-form-group">

                    <label for="username">
                        Nom d'utilisateur
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="100"
                        required
                    >

                </div>


                <!-- Email -->

                <div class="admin-form-group">

                    <label for="email">
                        Adresse e-mail
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="255"
                        required
                    >

                </div>


                <!-- Validation -->

                <div class="admin-form-group">

                    <label class="admin-checkbox">

                        <input
                            type="checkbox"
                            name="valid"
                            value="1"
                            <?= $valid === 1 ? 'checked' : '' ?>
                        >

                        <span>
                            Compte validé
                        </span>

                    </label>

                </div>

            </div>

        </div>


        <!-- =====================================================
             INFORMATIONS TECHNIQUES
        ====================================================== -->

        <div class="admin-user-card">

            <div class="admin-user-card-header">

                <h2>
                    <i class="fa-solid fa-circle-info"></i>
                    Informations techniques
                </h2>

            </div>


            <div class="admin-user-card-body">

                <div class="admin-info-row">

                    <span class="admin-info-label">
                        Hash Key
                    </span>

                    <code>
                        <?= htmlspecialchars($hashKey, ENT_QUOTES, 'UTF-8') ?>
                    </code>

                </div>


                <div class="admin-info-row">

                    <span class="admin-info-label">
                        Authentification 2FA
                    </span>

                    <?php if ($twoFactor === 1): ?>

                        <span class="admin-status active">
                            <i class="fa-solid fa-shield-halved"></i>
                            Activée
                        </span>

                    <?php else: ?>

                        <span class="admin-status inactive">
                            <i class="fa-solid fa-shield"></i>
                            Désactivée
                        </span>

                    <?php endif; ?>

                </div>

            </div>

        </div>


        <!-- =====================================================
             ACTIONS
        ====================================================== -->

        <div class="admin-user-actions">

            <button
                type="submit"
                class="admin-user-button primary"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                Enregistrer les modifications
            </button>

            <a
                href="/admin/users"
                class="admin-user-button secondary"
            >
                <i class="fa-solid fa-xmark"></i>
                Annuler
            </a>

        </div>

    </form>


    <!-- =========================================================
         SÉCURITÉ / 2FA
    ========================================================== -->

    <?php if ($twoFactor === 1): ?>

        <div class="admin-user-security-action">

            <div>

                <h3>
                    <i class="fa-solid fa-shield-halved"></i>
                    Authentification à deux facteurs
                </h3>

                <p>
                    L'utilisateur utilise actuellement l'authentification
                    à deux facteurs.
                </p>

                <p class="admin-security-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    La réinitialisation supprimera la configuration 2FA
                    actuellement enregistrée pour ce compte.
                </p>

            </div>


            <form
                method="post"
                action="/admin/users/2fa/reset"
                onsubmit="return confirm('Voulez-vous vraiment réinitialiser le 2FA de cet utilisateur ?');"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>"
                >

                <input
                    type="hidden"
                    name="hash_key"
                    value="<?= htmlspecialchars($hashKey, ENT_QUOTES, 'UTF-8') ?>"
                >

                <button
                    type="submit"
                    class="admin-user-button danger"
                >
                    <i class="fa-solid fa-shield-xmark"></i>
                    Réinitialiser le 2FA
                </button>

            </form>

        </div>

    <?php endif; ?>

</div>