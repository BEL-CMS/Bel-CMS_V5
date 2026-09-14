<?php

use BelCMS\system\Common;

$member = $member ?? null;
$social = $social ?? null;
$notFound = $notFound ?? false;

function membres_profile_e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function membres_profile_media(?string $value): ?string
{
    $value = trim((string) $value);

    if ($value === '') {
        return null;
    }

    if (preg_match('~^(?:https?:)?//~i', $value) === 1 || str_starts_with($value, '/')) {
        return $value;
    }

    return '/' . ltrim($value, '/');
}

function membres_social_url(string $network, string $value): string
{
    $value = trim($value);

    if (preg_match('~^https?://~i', $value) === 1) {
        return $value;
    }

    $prefixes = [
        'facebook'  => 'https://facebook.com/',
        'youtube'   => 'https://youtube.com/',
        'whatsapp'  => 'https://wa.me/',
        'instagram' => 'https://instagram.com/',
        'messenger' => 'https://m.me/',
        'tiktok'    => 'https://tiktok.com/@',
        'snapchat'  => 'https://snapchat.com/add/',
        'telegram'  => 'https://t.me/',
        'pinterest' => 'https://pinterest.com/',
        'x_twitter' => 'https://x.com/',
        'reddit'    => 'https://reddit.com/u/',
        'linkedIn'  => 'https://linkedin.com/in/',
        'skype'     => 'https://skype.com/',
        'viber'     => 'viber://chat?number=',
        'teams_ms'  => 'https://teams.microsoft.com/',
        'discord'   => 'https://discord.com/users/',
        'twitch'    => 'https://twitch.tv/',
    ];

    return ($prefixes[$network] ?? '') . ltrim($value, '/');
}
?>

<div class="members-page member-profile-page">
    <?php if ($notFound || $member === null): ?>
        <div class="member-not-found">
            <i class="fa-solid fa-user-slash"></i>
            <h1>Membre introuvable</h1>
            <p>Ce profil n'existe pas ou n'est plus disponible.</p>
            <a class="member-button" href="/members">
                <i class="fa-solid fa-arrow-left"></i>
                Retour aux membres
            </a>
        </div>
    <?php else: ?>
        <?php
            $username = trim((string) ($member->username ?? 'Membre'));
            $avatar = membres_profile_media($member->avatar ?? $language->get('USER_NO_AVATAR'));
            $cover = membres_profile_media($member->cover_avatar ?? $language->get('USER_BG_NONE'));
            $country = trim((string) ($member->country ?? ''));
            $lastName = trim((string) ($member->last_name ?? ''));
            $infos = trim((string) ($member->infos_text ?? ''));
            $website = trim((string) ($member->websites ?? ''));
            $publicMail = trim((string) ($member->public_mail ?? ''));
            $profils = trim((string) ($member->profils ?? ''));
            $registration = trim((string) Common::TransformDate($member->date_registration,'MEDIUM','MEDIUM') ?? '');
        ?>

        <section class="member-profile-head">
            <div class="member-cover<?= $cover !== null ? ' has-cover' : '' ?>"<?= $cover !== null ? ' style="background-image:url(\'' . membres_profile_e($cover) . '\')"' : '' ?>></div>
            <div class="member-profile-head-inner">
                <div class="member-profile-avatar">
                    <?php if ($avatar !== null): ?>
                        <img src="<?= membres_profile_e($avatar) ?>" alt="Avatar de <?= membres_profile_e($username) ?>">
                    <?php else: ?>
                        <i class="fa-solid fa-user"></i>
                    <?php endif; ?>
                </div>
                <div class="member-profile-identity">
                    <span class="members-eyebrow">PROFIL PUBLIC</span>
                    <h1><?= membres_profile_e($username) ?></h1>
                    <?php if ($lastName !== '' || $country !== ''): ?>
                        <p>
                            <?php if ($lastName !== ''): ?><?= membres_profile_e($lastName) ?><?php endif; ?>
                            <?php if ($lastName !== '' && $country !== ''): ?> · <?php endif; ?>
                            <?php if ($country !== ''): ?><i class="fa-solid fa-location-dot"></i> <?= membres_profile_e($country) ?><?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <div class="member-profile-layout">
            <main class="member-profile-main">
                <?php if ($infos !== ''): ?>
                    <section class="member-info-card">
                        <div class="member-section-title"><i class="fa-solid fa-user-pen"></i><h2>Présentation</h2></div>
                        <div class="member-text"><?= nl2br(membres_profile_e($infos)) ?></div>
                    </section>
                <?php endif; ?>

                <section class="member-info-card">
                    <div class="member-section-title"><i class="fa-solid fa-circle-info"></i><h2>Informations</h2></div>
                    <div class="member-details-grid">
                        <?php if ($country !== ''): ?>
                            <div><span>Pays</span><strong><?= membres_profile_e($country) ?></strong></div>
                        <?php endif; ?>
                        <?php if ($profils !== ''): ?>
                            <div><span>Profil</span><strong><?= membres_profile_e($profils) ?></strong></div>
                        <?php endif; ?>
                        <?php if ($registration !== ''): ?>
                            <div><span>Inscription</span><strong><?= membres_profile_e($registration) ?></strong></div>
                        <?php endif; ?>
                        <?php if ($publicMail !== ''): ?>
                            <div><span>E-mail public</span><strong><a href="mailto:<?= membres_profile_e($publicMail) ?>"><?= membres_profile_e($publicMail) ?></a></strong></div>
                        <?php endif; ?>
                        <?php if ($website !== ''): ?>
                            <div><span>Site internet</span><strong><a href="<?= membres_profile_e(preg_match('~^https?://~i', $website) ? $website : 'https://' . $website) ?>" target="_blank" rel="noopener noreferrer"><?= membres_profile_e($website) ?></a></strong></div>
                        <?php endif; ?>
                    </div>
                </section>
            </main>

            <aside class="member-profile-side">
                <?php
                    $socialFields = [
                        'facebook'  => ['Facebook', 'fa-brands fa-facebook-f'],
                        'youtube'   => ['YouTube', 'fa-brands fa-youtube'],
                        'instagram' => ['Instagram', 'fa-brands fa-instagram'],
                        'whatsapp'  => ['WhatsApp', 'fa-brands fa-whatsapp'],
                        'tiktok'    => ['TikTok', 'fa-brands fa-tiktok'],
                        'telegram'  => ['Telegram', 'fa-brands fa-telegram'],
                        'pinterest' => ['Pinterest', 'fa-brands fa-pinterest'],
                        'x_twitter' => ['X / Twitter', 'fa-brands fa-x-twitter'],
                        'reddit'    => ['Reddit', 'fa-brands fa-reddit-alien'],
                        'linkedIn'  => ['LinkedIn', 'fa-brands fa-linkedin-in'],
                        'discord'   => ['Discord', 'fa-brands fa-discord'],
                        'twitch'    => ['Twitch', 'fa-brands fa-twitch'],
                    ];
                ?>

                <?php if (is_object($social)): ?>
                    <section class="member-social-card">
                        <div class="member-section-title"><i class="fa-solid fa-share-nodes"></i><h2>Réseaux sociaux</h2></div>
                        <div class="member-social-links">
                            <?php foreach ($socialFields as $field => [$label, $icon]): ?>
                                <?php $value = trim((string) ($social->{$field} ?? '')); ?>
                                <?php if ($value !== ''): ?>
                                    <a href="<?= membres_profile_e(membres_social_url($field, $value)) ?>" target="_blank" rel="noopener noreferrer" class="member-social-link">
                                        <i class="<?= membres_profile_e($icon) ?>"></i>
                                        <span><?= membres_profile_e($label) ?></span>
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <a class="member-back-link" href="/members">
                    <i class="fa-solid fa-arrow-left"></i>
                    Tous les membres
                </a>
            </aside>
        </div>
    <?php endif; ?>
</div>
