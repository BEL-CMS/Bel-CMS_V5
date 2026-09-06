<?php

declare(strict_types=1);
?>

<div class="user-login">

    <div class="user-login-header">

        <span class="user-login-label">
            Bel-CMS V5
        </span>

        <h1>
            Vérification de sécurité
        </h1>

        <p>
            Utilisez votre application d'authentification
            ou l'un de vos codes de récupération.
        </p>

    </div>


    <div class="user-login-card">

        <?php if (!empty($error)): ?>

            <div class="user-login-error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <!-- Google Authenticator -->

        <form
            method="post"
            action="/user/login/2fa"
            class="user-login-form"
        >

            <div class="user-login-group">

                <label for="code">
                    Code Google Authenticator
                </label>

                <input
                    type="text"
                    id="code"
                    name="code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    placeholder="123456"
                    autofocus
                >

            </div>


            <button
                type="submit"
                class="user-login-button"
            >
                Vérifier le code
            </button>

        </form>


        <div class="user-login-divider">
            <span>
                ou
            </span>
        </div>


        <!-- Recovery Code -->

        <form
            method="post"
            action="/user/login/2fa"
            class="user-login-form"
        >

            <div class="user-login-group">

                <label for="recovery_code">
                    Code de récupération
                </label>

                <input
                    type="text"
                    id="recovery_code"
                    name="recovery_code"
                    autocomplete="off"
                    maxlength="14"
                    placeholder="XXXX-XXXX-XXXX"
                >

            </div>


            <button
                type="submit"
                class="user-button user-button-secondary"
            >
                Utiliser un code de récupération
            </button>

        </form>


        <div class="user-login-footer">

            <a href="/user/login">
                ← Retour à la connexion
            </a>

        </div>

    </div>

</div>