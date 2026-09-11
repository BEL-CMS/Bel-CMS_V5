<?php
declare(strict_types=1);
?>

<div class="user-login">
    <div class="user-login-header">
        <span class="user-login-label">Bel-CMS V5</span>
        <h1>Créer un compte</h1>
        <p>Rejoignez Bel-CMS et commencez votre expérience.</p>
    </div>
    <div class="user-login-card">
        <?php if (!empty($errors)): ?>
            <div class="user-login-error">
                <?php foreach ($errors as $error): ?>
                    <div>
                        <?= htmlspecialchars(
                            (string) $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($success !== null): ?>
            <div class="user-login-success">
                <?= htmlspecialchars(
                    (string) $success,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </div>
        <?php endif; ?>
        <form method="post" action="/user/register" class="user-login-form" autocomplete="off">
            <?= csrf_field(); ?>
            <div class="user-login-group">
                <label for="username">
                    Nom d'utilisateur
                </label>
                <input type="text" id="username" name="username"
                    value="<?= htmlspecialchars(
                        (string) $username,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>" placeholder="Votre nom d'utilisateur" maxlength="100" autocomplete="username" required autofocus>
            </div>
            <div class="user-login-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars(
                        (string) $email,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>" placeholder="votre@email.com" maxlength="255" autocomplete="email" required>
            </div>
            <div class="user-login-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe" minlength="8" autocomplete="new-password" required>
                <small>Minimum 8 caractères.</small>
            </div>
            <div class="user-login-group">
                <label for="password_confirm">Confirmation du mot de passe</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirmez votre mot de passe" minlength="8" autocomplete="new-password" required>
            </div>
            <div id="belcms_global_captcha">
                <div id="belcms_global_captcha_style">
                    <span>Il faut passer par une vérification de sécurité.</span>
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text">Résolvez le calcul : <?= $_SESSION['CAPTCHA']['question'] ?? 'Chargement...' ?></span>
                        <input type="number" name="captcha" class="form-control" placeholder="Votre réponse" required>
                    </div>
                    <div class="input-group mb-3">
                        <div class="belcms_captcha_container">
                            <label>Captcha</label>
                            <input type="range" id="belcms_captcha_slider" min="0" max="100" value="15">
                            <div id="belcms_captcha_percent">15%</div>
                            <input type="hidden" name="belcms_captcha_value" id="belcms_captcha_value">
                            <input type="hidden" name="captcha_value" value="">
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="user-login-button">
                <i class="fa-solid fa-user-plus"></i> Créer mon compte
            </button>
        </form>
        <div class="user-login-footer">
            Vous avez déjà un compte ?
            <a href="/user/login">Se connecter</a>
        </div>
    </div>
</div>