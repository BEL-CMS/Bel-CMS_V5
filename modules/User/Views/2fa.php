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

/**
 * @var bool        $enabled
 * @var string|null $secret
 * @var string|null $uri
 * @var string|null $error
 * @var string|null $success
 */
?>

<div class="user-2fa">

    <div class="user-page-header">

        <div>
            <h1>
                <i class="fa-solid fa-shield-halved"></i>
                Authentification à deux facteurs
            </h1>

            <p>
                Protégez votre compte avec une authentification supplémentaire.
            </p>
        </div>

        <a
            href="/user/security"
            class="user-btn-secondary"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Retour
        </a>

    </div>


    <?php if (!empty($error)): ?>

        <div class="user-message user-message-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>

    <?php endif; ?>


    <?php if (!empty($success)): ?>

        <div class="user-message user-message-success">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>

    <?php endif; ?>


    <?php if ($enabled): ?>

        <!-- =================================================
             2FA ACTIVE
             ================================================= -->

        <section class="user-security-card">

            <div class="user-security-card-header">

                <div class="user-security-card-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>

                <div>
                    <h3>
                        Authentification à deux facteurs activée
                    </h3>

                    <p>
                        Votre compte bénéficie actuellement d'une protection supplémentaire.
                    </p>
                </div>

            </div>


            <div class="user-security-card-content">

                <div class="user-security-status user-security-status-success">
                    <i class="fa-solid fa-circle-check"></i>

                    La double authentification est active
                </div>


                <p class="user-security-description">
                    À chaque nouvelle connexion, un code à 6 chiffres généré par votre application d'authentification vous sera demandé.
                </p>


                <div class="user-2fa-enabled-box">

                    <div class="user-2fa-enabled-icon">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </div>

                    <div>

                        <strong>
                            Application d'authentification
                        </strong>

                        <p>
                            Votre compte est associé à une application telle que Google Authenticator.
                        </p>

                    </div>

                </div>


                <div class="user-2fa-disable">

                    <div>

                        <strong>
                            Désactiver l'authentification à deux facteurs
                        </strong>

                        <p>
                            La désactivation supprimera la clé de sécurité actuellement enregistrée.
                        </p>

                    </div>


                    <form
                        method="post"
                        action="/user/security/2fa"
                        onsubmit="return confirm('Êtes-vous certain de vouloir désactiver l’authentification à deux facteurs ?');"
                    >

                        <input
                            type="hidden"
                            name="action"
                            value="disable"
                        >

                        <div class="user-form-group">

                            <label for="disable-password">
                                Mot de passe actuel
                            </label>

                            <input
                                type="password"
                                id="disable-password"
                                name="password"
                                autocomplete="current-password"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="user-button user-button-danger"
                        >
                            <i class="fa-solid fa-unlock"></i>
                            Désactiver la 2FA
                        </button>

                    </form>

                </div>

            </div>

        </section>


    <?php else: ?>

        <!-- =================================================
             2FA CONFIGURATION
             ================================================= -->

        <section class="user-security-card">

            <div class="user-security-card-header">

                <div class="user-security-card-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                <div>

                    <h3>
                        Configurer l'authentification à deux facteurs
                    </h3>

                    <p>
                        Suivez les étapes ci-dessous pour sécuriser votre compte.
                    </p>

                </div>

            </div>


            <div class="user-security-card-content">

                <div class="user-2fa-steps">

                    <div class="user-2fa-step">

                        <span class="user-2fa-step-number">
                            1
                        </span>

                        <div>

                            <strong>
                                Ouvrez votre application d'authentification
                            </strong>

                            <p>
                                Utilisez Google Authenticator ou une application compatible TOTP.
                            </p>

                        </div>

                    </div>


                    <div class="user-2fa-step">

                        <span class="user-2fa-step-number">
                            2
                        </span>

                        <div>

                            <strong>
                                Scannez le QR Code
                            </strong>

                            <p>
                                Scannez le code ci-dessous avec votre application.
                            </p>

                        </div>

                    </div>

                </div>


                <?php if (!empty($uri)): ?>

                    <div class="user-2fa-content">

                        <div class="user-2fa-qrcode">

                            <?= \BelCMS\Core\Security\QRCode\QRCode::make($uri)->svg() ?>

                        </div>


                        <div>

                            <div class="user-2fa-secret">

                                <span>
                                    Clé de configuration
                                </span>

                                <strong>
                                    <?= htmlspecialchars(
                                        (string)$secret,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                                <p>
                                    Si vous ne pouvez pas scanner le QR Code, vous pouvez saisir cette clé manuellement dans votre application d'authentification.
                                </p>

                            </div>


                            <div class="user-2fa-next">

                                <p>
                                    Une fois le compte ajouté dans votre application, saisissez le code à 6 chiffres affiché.
                                </p>


                                <form
                                    method="post"
                                    action="/user/security/2fa"
                                >

                                    <label for="2fa-code">
                                        Code de vérification
                                    </label>

                                    <input
                                        type="text"
                                        id="2fa-code"
                                        name="code"
                                        inputmode="numeric"
                                        autocomplete="one-time-code"
                                        pattern="[0-9]{6}"
                                        maxlength="6"
                                        placeholder="000000"
                                        required
                                    >

                                    <button
                                        type="submit"
                                        class="user-button user-button-primary"
                                    >
                                        <i class="fa-solid fa-check"></i>
                                        Activer la 2FA
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                <?php endif; ?>

            </div>

        </section>

    <?php endif; ?>

</div>