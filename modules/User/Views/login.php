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
            Connectez-vous à votre espace utilisateur.
        </p>

    </div>


    <div class="user-login-card">

        <?php if (!empty($error)): ?>

            <div class="user-login-error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <form
            method="post"
            action="/user/login"
            class="user-login-form"
        >

            <div class="user-login-group">

                <label for="identifier">
                    Identifiant ou adresse email
                </label>

                <input
                    type="text"
                    id="identifier"
                    name="identifier"
                    value="<?= htmlspecialchars(
                        $_POST['identifier'] ?? ''
                    ) ?>"
                    placeholder="Votre identifiant ou email"
                    autocomplete="username"
                    required
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
                Se connecter
            </button>

        </form>


        <div class="user-login-footer">

            Pas encore de compte ?
            <a href="#">
                Créer un compte
            </a>

        </div>

    </div>

</div>