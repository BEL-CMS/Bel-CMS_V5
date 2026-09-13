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

$social = $social ?? null;
$language = $language ?? null;
$errors = $errors ?? [];
$success = $success ?? null;

$fields = [
    'facebook'  => ['Facebook', 'fa-brands fa-facebook', 'https://facebook.com/...'],
    'youtube'   => ['YouTube', 'fa-brands fa-youtube', 'https://youtube.com/...'],
    'whatsapp'  => ['WhatsApp', 'fa-brands fa-whatsapp', 'https://wa.me/...'],
    'instagram' => ['Instagram', 'fa-brands fa-instagram', 'https://instagram.com/...'],
    'messenger' => ['Messenger', 'fa-brands fa-facebook-messenger', 'https://m.me/...'],
    'tiktok'    => ['TikTok', 'fa-brands fa-tiktok', 'https://tiktok.com/@...'],
    'snapchat'  => ['Snapchat', 'fa-brands fa-snapchat', 'https://snapchat.com/...'],
    'telegram'  => ['Telegram', 'fa-brands fa-telegram', 'https://t.me/...'],
    'pinterest' => ['Pinterest', 'fa-brands fa-pinterest', 'https://pinterest.com/...'],
    'x_twitter' => ['X / Twitter', 'fa-brands fa-x-twitter', 'https://x.com/...'],
    'reddit'    => ['Reddit', 'fa-brands fa-reddit', 'https://reddit.com/u/...'],
    'linkedIn'  => ['LinkedIn', 'fa-brands fa-linkedin', 'https://linkedin.com/in/...'],
    'skype'     => ['Skype', 'fa-brands fa-skype', 'Identifiant Skype'],
    'viber'     => ['Viber', 'fa-brands fa-viber', 'Lien / identifiant Viber'],
    'teams_ms'  => ['Microsoft Teams', 'fa-brands fa-microsoft', 'Compte Teams'],
    'discord'   => ['Discord', 'fa-brands fa-discord', 'https://discord.gg/...'],
    'twitch'    => ['Twitch', 'fa-brands fa-twitch', 'https://twitch.tv/...'],
];
?>

<div class="profile-page social-edit-page">

    <div class="profile-header social-header">
        <div class="profile-header-content">
            <div class="profile-user-info">
                <h1>
                    <i class="fa-solid fa-share-nodes"></i>
                    Modifier mes réseaux sociaux
                </h1>
                <p>Gère les liens associés à ton profil.</p>
            </div>
        </div>

        <div class="profile-header-actions">
            <a href="/profils/social" class="profile-button">
                <i class="fa-solid fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="profile-section">

        <?php if ($success): ?>
            <div class="social-alert social-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="social-alert social-alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <?php foreach ((array) $errors as $error): ?>
                    <div><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/profils/editsocial" class="social-edit-form">

            <?= csrf_field() ?>

            <div class="social-edit-grid">

                <?php foreach ($fields as $field => [$label, $icon, $placeholder]): ?>

                    <div class="social-edit-card">
                        <div class="social-edit-card-header">
                            <i class="<?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>"></i>
                            <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>

                        <div class="social-edit-card-value">
                            <input
                                type="text"
                                id="social_<?= htmlspecialchars($field, ENT_QUOTES, 'UTF-8') ?>"
                                name="<?= htmlspecialchars($field, ENT_QUOTES, 'UTF-8') ?>"
                                value="<?= htmlspecialchars(
                                    is_object($social) ? (string) ($social->{$field} ?? '') : '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                placeholder="<?= htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') ?>"
                            >
                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

            <div class="social-edit-actions">
                <a href="/profils/social" class="profile-button">
                    Annuler
                </a>

                <button type="submit" class="profile-button profile-button-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Enregistrer
                </button>
            </div>

        </form>
    </div>
</div>
