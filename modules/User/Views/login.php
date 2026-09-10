<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @link https://determe.be
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
 */

declare(strict_types=1);
?>

<div class="user-login">

    <div class="user-login-header">

        <span class="user-login-label">
            Bel-CMS V5
        </span>

        <h1>
            Connexion
        </h1>

        <p>
            Connectez-vous à votre espace personnel.
        </p>

    </div>


    <div class="user-login-card">

        <?php if (!empty($error)): ?>

            <div class="user-login-error">
                <?= htmlspecialchars(
                    (string) $error,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </div>

        <?php endif; ?>


        <form
            method="post"
            action="/user/login"
            class="user-login-form"
            autocomplete="off"
        >

            <?= csrf_field(); ?>


            <div class="user-login-group">

                <label for="identifier">
                    Identifiant ou adresse e-mail
                </label>

                <input
                    type="text"
                    id="identifier"
                    name="identifier"
                    value="<?= htmlspecialchars(
                        (string) $username,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    placeholder="Nom d'utilisateur ou e-mail"
                    autocomplete="username"
                    required
                    autofocus
                >

            </div>


            <div class="user-login-group">

                <label for="password">
                    Mot de passe
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Votre mot de passe"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="user-login-button"
            >
                <i class="fa-solid fa-right-to-bracket"></i>
                Se connecter
            </button>

        </form>


        <div class="user-login-footer">

            <a href="/user/register">
                <i class="fa-solid fa-user-plus"></i>
                Créer un compte
            </a>

        </div>

    </div>

</div>