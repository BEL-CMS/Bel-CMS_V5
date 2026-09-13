<?php

declare(strict_types=1);

$social = $social ?? null;
$language = $language ?? null;

$trans = static function (string $key, string $fallback) use ($language): string {
    if ($language && method_exists($language, 'get')) {
        return (string) $language->get($key, $fallback);
    }

    return $fallback;
};

$items = [
    'facebook'  => ['Facebook', 'fa-brands fa-facebook', '#1877f2'],
    'youtube'   => ['YouTube', 'fa-brands fa-youtube', '#ff0000'],
    'whatsapp'  => ['WhatsApp', 'fa-brands fa-whatsapp', '#25d366'],
    'instagram' => ['Instagram', 'fa-brands fa-instagram', '#e4405f'],
    'messenger' => ['Messenger', 'fa-brands fa-facebook-messenger', '#0084ff'],
    'tiktok'    => ['TikTok', 'fa-brands fa-tiktok', '#ffffff'],
    'snapchat'  => ['Snapchat', 'fa-brands fa-snapchat', '#fffc00'],
    'telegram'  => ['Telegram', 'fa-brands fa-telegram', '#229ed9'],
    'pinterest' => ['Pinterest', 'fa-brands fa-pinterest', '#e60023'],
    'x_twitter' => ['X / Twitter', 'fa-brands fa-x-twitter', '#ffffff'],
    'reddit'    => ['Reddit', 'fa-brands fa-reddit', '#ff4500'],
    'linkedIn'  => ['LinkedIn', 'fa-brands fa-linkedin', '#0a66c2'],
    'skype'     => ['Skype', 'fa-brands fa-skype', '#00aff0'],
    'viber'     => ['Viber', 'fa-brands fa-viber', '#7360f2'],
    'teams_ms'  => ['Microsoft Teams', 'fa-brands fa-microsoft', '#6264a7'],
    'discord'   => ['Discord', 'fa-brands fa-discord', '#5865f2'],
    'twitch'    => ['Twitch', 'fa-brands fa-twitch', '#9146ff'],
];

$username = (string) ($_SESSION['USER']->username ?? '');
$avatar = (string) ($_SESSION['USER']->profile->avatar ?? '');
?>

<div class="profile-page social-page">

    <div class="profile-header social-header">

        <div class="profile-header-content">

            <div class="profile-avatar-wrapper">
                <div class="profile-avatar">
                    <?php if ($avatar !== ''): ?>
                        <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar">
                    <?php else: ?>
                        <i class="fa-solid fa-user"></i>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-user-info">
                <h1>
                    <i class="fa-solid fa-share-nodes"></i>
                    <?= htmlspecialchars($trans('profile.social_networks', 'Réseaux sociaux'), ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></p>
            </div>

        </div>

        <div class="profile-header-actions">
            <a href="/profils" class="profile-button">
                <i class="fa-solid fa-arrow-left"></i>
                Retour au profil
            </a>
            <a href="/profils/editsocial" class="profile-button profile-button-primary">
                <i class="fa-solid fa-pen"></i>
                Modifier
            </a>
        </div>

    </div>

    <div class="social-content">

        <div class="profile-section social-intro">
            <div class="profile-section-title">
                <i class="fa-solid fa-share-nodes"></i>
                <span>Mes réseaux sociaux</span>
            </div>
            <p class="social-description">
                Retrouvez ici les réseaux sociaux associés à votre profil.
            </p>
        </div>

        <?php
        $hasSocial = false;
        if (is_object($social)) {
            foreach ($items as $field => $_config) {
                if (trim((string) ($social->{$field} ?? '')) !== '') {
                    $hasSocial = true;
                    break;
                }
            }
        }
        ?>

        <?php if (!$hasSocial): ?>

            <div class="profile-section social-empty">
                <div class="social-empty-icon">
                    <i class="fa-solid fa-link-slash"></i>
                </div>
                <h3>Aucun réseau social renseigné</h3>
                <p>Ajoute tes réseaux sociaux depuis le bouton Modifier.</p>
            </div>

        <?php else: ?>

            <div class="social-grid">
                <?php foreach ($items as $field => [$label, $icon, $color]): ?>
                    <?php
                    $value = is_object($social) ? trim((string) ($social->{$field} ?? '')) : '';
                    if ($value === '') {
                        continue;
                    }

                    $href = $value;
                    if (!preg_match('~^[a-z][a-z0-9+.-]*://~i', $href)) {
                        $href = 'https://' . ltrim($href, '/');
                    }
                    ?>
                    <div class="social-card">
                        <div class="social-card-icon" style="--social-color: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>;">
                            <i class="<?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>"></i>
                        </div>

                        <div class="social-card-content">
                            <div class="social-card-title"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="social-card-value"><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?></div>
                        </div>

                        <div class="social-card-action">
                            <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="social-open"
                               title="Ouvrir">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</div>
