<?php

declare(strict_types=1);
use BelCMS\Core\Security\QRCode\QRCode;

?>

<div class="user-2fa">

    <div class="user-edit-header">

        <a
            href="/user/security"
            class="user-profile-back"
        >
            ← Retour à la sécurité
        </a>

        <span class="user-profile-label">
            Authentification
        </span>

        <h1>
            Authentification à deux facteurs
        </h1>

        <p>
            Ajoutez une protection supplémentaire à votre compte.
        </p>

    </div>


    <?php if (!empty($enabled)): ?>

        <section class="user-security-card">

            <h2>
                Le 2FA est déjà activé
            </h2>

            <p>
                Votre compte est actuellement protégé
                par l'authentification à deux facteurs.
            </p>

        </section>

    <?php else: ?>

        <section class="user-security-card">

            <div class="user-2fa-introduction">

                <span class="user-profile-card-label">
                    Étape 1
                </span>

                <h2>
                    Configurez votre application
                </h2>

                <p>
                    Scannez le QR Code avec votre application
                    d'authentification.
                </p>

            </div>


            <div class="user-2fa-content">

                <div class="user-2fa-qrcode">

                    <?php if (!empty($uri)): ?>

                        <?php
                        $qrcode = QRCode::make($uri);

                        echo $qrcode->svg();
                        ?>

                    <?php endif; ?>

                </div>


                <div class="user-2fa-secret">

                    <span>
                        Clé de configuration
                    </span>

                    <strong>
                        <?= htmlspecialchars($secret ?? '') ?>
                    </strong>

                    <p>
                        Si vous ne pouvez pas scanner le QR Code,
                        saisissez cette clé manuellement dans
                        votre application.
                    </p>

                </div>

            </div>


            <div class="user-2fa-next">

                <p>
                    Une fois le compte ajouté dans votre application,
                    vous recevrez un code à 6 chiffres.
                </p>

                <form
                    method="post"
                    action="/user/security/2fa"
                >

                    <label for="code">
                        Code de vérification
                    </label>

                    <input
                        type="text"
                        id="code"
                        name="code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        placeholder="123456"
                        required
                    >

                    <button
                        type="submit"
                        class="user-button user-button-primary"
                    >
                        Vérifier et activer
                    </button>

                </form>

            </div>

<?php if (!empty($error)): ?>

    <div class="user-message user-message-error">
        <?= htmlspecialchars($error) ?>
    </div>

<?php endif; ?>


<?php if (!empty($success)): ?>

    <div class="user-message user-message-success">
        <?= htmlspecialchars($success) ?>
    </div>

<?php endif; ?>

        </section>

    <?php endif; ?>

</div>