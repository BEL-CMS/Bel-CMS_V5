<?php

declare(strict_types=1);

$targetUser = $targetUser ?? null;

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

    <div class="admin-users-header">

        <h1>
            <i class="fa-solid fa-user-pen"></i>
            Modifier l'utilisateur
        </h1>

        <p>
            Modification du compte
            <strong>
                <?= htmlspecialchars(
                    $username,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>
        </p>

    </div>


    <?php if ($saved): ?>

        <div class="admin-user-alert success">
            <i class="fa-solid fa-circle-check"></i>
            Les modifications ont été enregistrées.
        </div>

    <?php endif; ?>


    <form
        method="post"
        action="/admin/users/edit?hash_key=<?= urlencode($hashKey) ?>"
        class="admin-user-form"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                csrf_token(),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <input
            type="hidden"
            name="hash_key"
            value="<?= htmlspecialchars(
                $hashKey,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >


        <div class="admin-user-form-group">

            <label for="username">
                Nom d'utilisateur
            </label>

            <input
                type="text"
                id="username"
                name="username"
                value="<?= htmlspecialchars(
                    $username,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                maxlength="100"
                required
            >

        </div>


        <div class="admin-user-form-group">

            <label for="email">
                Adresse email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars(
                    $email,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                maxlength="255"
                required
            >

        </div>


        <div class="admin-user-form-group">

            <label>
                État du compte
            </label>

            <label class="admin-user-switch">

                <input
                    type="checkbox"
                    name="valid"
                    value="1"
                    <?= $valid === 1 ? 'checked' : '' ?>
                >

                <span>
                    Compte actif
                </span>

            </label>

        </div>


        <div class="admin-user-info">

            <div>
                <span>Hash key</span>

                <code>
                    <?= htmlspecialchars(
                        $hashKey,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </code>
            </div>

            <div>
                <span>Authentification 2FA</span>

                <?php if ($twoFactor === 1): ?>

                    <strong>
                        <i class="fa-solid fa-shield-halved"></i>
                        Activée
                    </strong>

                <?php else: ?>

                    <strong>
                        <i class="fa-solid fa-shield"></i>
                        Désactivée
                    </strong>

                <?php endif; ?>

            </div>

        </div>


        <div class="admin-user-form-actions">

            <a
                href="/admin/users"
                class="admin-user-button secondary"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Retour
            </a>

            <button
                type="submit"
                class="admin-user-button primary"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                Enregistrer
            </button>

        </div>

    </form>

</div>