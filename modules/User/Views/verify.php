<?php
declare(strict_types=1);
?>

<div class="user-login">

    <div class="user-login-header">

        <span class="user-login-label">
            Bel-CMS V5
        </span>

        <h1>
            Validation du compte
        </h1>

        <p>
            Confirmation de votre inscription.
        </p>

    </div>


    <div class="user-login-card">

        <?php if (!empty($error)): ?>

            <div class="user-login-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?= htmlspecialchars(
                    (string)$error,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

            </div>

        <?php endif; ?>


        <?php if (!empty($success)): ?>

            <div class="user-login-success">

                <i class="fa-solid fa-circle-check"></i>

                <?= htmlspecialchars(
                    (string)$success,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>

            </div>


            <a
                href="/user/login"
                class="user-login-button"
            >
                <i class="fa-solid fa-right-to-bracket"></i>
                Se connecter
            </a>

        <?php endif; ?>

    </div>

</div>