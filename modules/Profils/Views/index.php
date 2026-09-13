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

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

/*
 * Profil utilisateur
 *
 * Variable attendue :
 * $profile
 *
 * Champs :
 * hash_key
 * last_name
 * username
 * gender
 * public_mail
 * websites
 * avatar
 * cover_avatar
 * infos_text
 * phone
 * birthday
 * country
 * date_registration
 * visits
 * gravatar
 * profils
 */

$profile = $_SESSION['USER']->profils ?? null;

if (!$profile) {
    ?>
    <div class="profile-page">
        <div class="profile-empty">
            <div class="profile-empty-icon">
                <i class="fa-solid fa-user-slash"></i>
            </div>

            <h2>Profil introuvable</h2>

            <p>
                Le profil demandé n'existe pas ou n'est plus disponible.
            </p>
        </div>
    </div>
    <?php
    return;
}

$username = (string)($profile->username ?? '');
$lastName = (string)($profile->last_name ?? '');
$gender = (string)($profile->gender ?? '');
$email = (string)($profile->public_mail ?? '');
$website = (string)($profile->websites ?? '');
$avatar = (string)($profile->avatar ?? '');
$cover = (string)($profile->cover_avatar ?? '');
$infos = (string)($profile->infos_text ?? '');
$phone = (string)($profile->phone ?? '');
$birthday = (string)($profile->birthday ?? '');
$country = (string)($profile->country ?? '');
$dateRegistration = (string)($profile->date_registration ?? '');
$visits = (int)($profile->visits ?? 0);
$gravatar = (string)($profile->gravatar ?? '');
$profileType = (string)($profile->profils ?? '');

if ($username === '') {
    $username = 'Utilisateur';
}

if ($avatar === '') {
    $avatar =  $language->get('USER_NO_AVATAR');
}

if ($cover === '') {
    $cover =  $language->get('USER_BG_NONE');
}

$fullName = trim($lastName);

if ($fullName !== '') {
    $displayName = $username . ' ' . $fullName;
} else {
    $displayName = $username;
}
?>

<div class="profile-page">
    <!-- =========================================
         COVER
    ========================================== -->
    <section class="profile-hero">
        <div
            class="profile-cover" style="background-image:
                linear-gradient(
                    180deg,
                    rgba(5, 12, 25, .15) 0%,
                    rgba(5, 12, 25, .65) 70%,
                    rgba(5, 12, 25, 1) 100%
                ),
                url('<?= htmlspecialchars(
                    $cover,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>');">
            <div class="profile-cover-grid"></div>
            <div class="profile-cover-glow"></div>
        </div>
        <!-- =========================================
             HEADER PROFIL
        ========================================== -->
        <div class="profile-header">
            <div class="profile-avatar">
                <img
                    src="<?= htmlspecialchars(
                        $avatar,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                    alt="<?= htmlspecialchars(
                        $displayName,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>"
                >
                <span class="profile-online"></span>
            </div>
            <div class="profile-header-info">
                <div class="profile-username">
                    <?= htmlspecialchars(
                        $username,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </div>
                <?php if ($lastName !== ''): ?>
                    <div class="profile-lastname">
                        <?= htmlspecialchars(
                            $lastName,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                <?php endif; ?>

                <?php if ($country !== ''): ?>
                    <div class="profile-location">
                        <i class="fa-solid fa-location-dot"></i>
                        <?= htmlspecialchars(
                            $country,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-header-action">
                <a
                    href="/profils/edit" class="profile-button profile-button-primary">
                    <i class="fa-solid fa-pen"></i>
                    Modifier le profil
                </a>
            </div>
        </div>
    </section>
    <!-- =========================================
         CONTENU
    ========================================== -->
    <div class="profile-content">
        <!-- =========================================
             COLONNE PRINCIPALE
        ========================================== -->
        <main class="profile-main">
            <!-- =========================================
                 PRÉSENTATION
            ========================================== -->
            <section class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <span class="profile-card-icon">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <div>
                            <h2>Présentation</h2>
                            <p>Informations personnelles</p>
                        </div>
                    </div>
                </div>
                <div class="profile-card-body">
                    <?php if ($infos !== ''): ?>
                        <div class="profile-description">
                            <?= nl2br(
                                htmlspecialchars(
                                    $infos,
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ); ?>
                        </div>
                    <?php else: ?>
                        <div class="profile-no-data">
                            <i class="fa-regular fa-comment"></i>
                            Aucune présentation disponible.
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            <!-- =========================================
                 INFORMATIONS
            ========================================== -->
            <section class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <span class="profile-card-icon">
                            <i class="fa-solid fa-id-card"></i>
                        </span>
                        <div>
                            <h2>Informations</h2>
                            <p>Informations publiques</p>
                        </div>
                    </div>
                </div>
                <div class="profile-card-body">
                    <div class="profile-info-grid">
                        <?php if ($lastName !== ''): ?>
                            <div class="profile-info-item">
                                <span class="profile-info-icon">
                                    <i class="fa-solid fa-user-tag"></i>
                                </span>
                                <div>
                                    <span class="profile-info-label">Nom</span>
                                    <strong>
                                        <?= htmlspecialchars(
                                            $lastName,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </strong>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($gender !== ''): ?>
                            <div class="profile-info-item">
                                <span class="profile-info-icon">
                                    <i class="fa-solid fa-venus-mars"></i>
                                </span>
                                <div>
                                    <span class="profile-info-label">Genre</span>
                                    <strong>
                                        <?= htmlspecialchars(
                                            $gender,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </strong>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($birthday !== ''): ?>
                            <div class="profile-info-item">
                                <span class="profile-info-icon">
                                    <i class="fa-solid fa-cake-candles"></i>
                                </span>
                                <div>
                                    <span class="profile-info-label">Date de naissance</span>
                                    <strong>
                                        <?= htmlspecialchars(
                                            $birthday,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </strong>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($country !== ''): ?>
                            <div class="profile-info-item">
                                <span class="profile-info-icon">
                                    <i class="fa-solid fa-earth-europe"></i>
                                </span>
                                <div>
                                    <span class="profile-info-label">Pays</span>
                                    <strong>
                                        <?= htmlspecialchars(
                                            $country,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </strong>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($email !== ''): ?>
                            <div class="profile-info-item">
                                <span class="profile-info-icon">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <div>
                                    <span class="profile-info-label">E-mail public</span>
                                    <strong>
                                        <a
                                            href="mailto:<?= htmlspecialchars(
                                                $email,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>"
                                        >
                                            <?= htmlspecialchars(
                                                $email,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </a>
                                    </strong>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($phone !== ''): ?>
                            <div class="profile-info-item">
                                <span class="profile-info-icon">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <div>
                                    <span class="profile-info-label">
                                        Téléphone
                                    </span>
                                    <strong>
                                        <?= htmlspecialchars(
                                            $phone,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </strong>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <!-- =========================================
                 SITE WEB
            ========================================== -->
            <?php if ($website !== ''): ?>
                <section class="profile-card">
                    <div class="profile-card-header">
                        <div class="profile-card-title">
                            <span class="profile-card-icon">
                                <i class="fa-solid fa-globe"></i>
                            </span>
                            <div>
                                <h2>Site internet</h2>
                                <p>Présence sur le web</p>
                            </div>
                        </div>
                    </div>
                    <div class="profile-card-body">
                        <a href="<?= htmlspecialchars(
                                $website,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            target="_blank" rel="noopener noreferrer" class="profile-website">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            <?= htmlspecialchars(
                                $website,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </a>
                    </div>
                </section>
            <?php endif; ?>
        </main>
        <!-- =========================================
             SIDEBAR
        ========================================== -->
        <aside class="profile-sidebar">
            <!-- STATISTIQUES -->
            <section class="profile-card profile-stats-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <span class="profile-card-icon">
                            <i class="fa-solid fa-chart-line"></i>
                        </span>
                        <div>
                            <h2>Statistiques</h2>
                            <p>Activité du profil</p>
                        </div>
                    </div>
                </div>
                <div class="profile-stats">
                    <div class="profile-stat">
                        <span class="profile-stat-icon">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                        <div>
                            <strong>
                                <?= number_format(
                                    $visits,
                                    0,
                                    ',',
                                    ' '
                                ); ?>
                            </strong>
                            <span>Visites</span>
                        </div>
                    </div>
                    <?php if ($dateRegistration !== ''): ?>
                        <div class="profile-stat">
                            <span class="profile-stat-icon">
                                <i class="fa-solid fa-calendar-plus"></i>
                            </span>
                            <div>
                                <strong>
                                    <?= htmlspecialchars(
                                        $dateRegistration,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </strong>
                                <span>Inscrit depuis</span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($profileType !== ''): ?>
                        <div class="profile-stat">
                            <span class="profile-stat-icon">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>
                            <div>
                                <strong>
                                    <?= htmlspecialchars(
                                        $profileType,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </strong>
                                <span>
                                    Type de profil
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            <!-- GRAVATAR -->
            <?php if ($gravatar !== ''): ?>
                <section class="profile-card">
                    <div class="profile-card-header">
                        <div class="profile-card-title">
                            <span class="profile-card-icon">
                                <i class="fa-solid fa-image"></i>
                            </span>
                            <div>
                                <h2>Avatar</h2>
                                <p>Source externe</p>
                            </div>
                        </div>
                    </div>
                    <div class="profile-card-body">
                        <a
                            href="<?= htmlspecialchars(
                                $gravatar,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="profile-external-link">
                            <i class="fa-solid fa-link"></i>
                            Voir la source
                        </a>
                    </div>
                </section>
            <?php endif; ?>
        </aside>
    </div>
</div>