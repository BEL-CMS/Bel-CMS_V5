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

$profile = $profile ?? null;

if (!$profile) {
    ?>
    <div class="profile-edit-page">
        <div class="profile-edit-empty">
            <div class="profile-edit-empty-icon">
                <i class="fa-solid fa-user-slash"></i>
            </div>
            <h2>Profil introuvable</h2>
            <p>Impossible de charger les informations de votre profil.</p>
        </div>

    </div>
    <?php
    return;
}
/*
 * =========================================================
 * DONNÉES
 * =========================================================
 */
$username = (string)($profile->username ?? '');
$lastName = (string)($profile->last_name ?? '');
$gender   = (string)($profile->gender ?? '');
$email    = (string)($profile->public_mail ?? '');
$website  = (string)($profile->websites ?? '');
$avatar   = (string)($profile->avatar ?? $language->get('USER_NO_AVATAR'));
$cover    = (string)($profile->cover_avatar ?? $language->get('USER_BG_NONE'));
$infos    = (string)($profile->infos_text ?? '');
$phone    = (string)($profile->phone ?? '');
$birthday = (string)($profile->birthday ?? '');
$country  = (string)($profile->country ?? '');
$gravatar = (string)($profile->gravatar ?? '');
$profil   = (string)($profile->profils ?? '');
/*
 * =========================================================
 * MESSAGES
 * =========================================================
 */
$errors  = $errors ?? [];
$success = $success ?? null;
/*
 * =========================================================
 * VALEURS APRÈS POST
 * =========================================================
 */
$username = (string)($usernameValue ?? $username);
$lastName = (string)($lastNameValue ?? $lastName);
$gender   = (string)($genderValue ?? $gender);
$email    = (string)($emailValue ?? $email);
$website  = (string)($websiteValue ?? $website);
$infos    = (string)($infosValue ?? $infos);
$phone    = (string)($phoneValue ?? $phone);
$birthday = (string)($birthdayValue ?? $birthday);
$country  = (string)($countryValue ?? $country);
$profil   = (string)($profilValue ?? $profil);
/*
 * =========================================================
 * HELPER
 * =========================================================
 */
$e = static function ($value): string {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
};
?>
<div class="profile-edit-page">
    <!-- =====================================================
         HEADER
    ====================================================== -->
    <div class="profile-edit-header">
        <div>
            <div class="profile-edit-title">
                <span class="profile-edit-title-icon">
                    <i class="fa-solid fa-user-pen"></i>
                </span>
                <div>
                    <h1>Modifier le profil</h1>
                    <p>Personnalisez les informations de votre profil.</p>
                </div>
            </div>
        </div>
        <a href="/profile" class="profile-edit-back">
            <i class="fa-solid fa-arrow-left"></i>
            Retour au profil
        </a>
    </div>
    <!-- =====================================================
         ALERTES
    ====================================================== -->
    <?php if (!empty($errors)): ?>
        <div class="profile-edit-alert profile-edit-alert-danger">
            <div class="profile-edit-alert-icon">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div class="profile-edit-alert-content">
                <?php foreach ($errors as $error): ?>
                    <div>
                        <?= $e($error); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($success !== null): ?>
        <div class="profile-edit-alert profile-edit-alert-success">
            <div class="profile-edit-alert-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="profile-edit-alert-content">
                <?= $e($success); ?>
            </div>
        </div>
    <?php endif; ?>
    <!-- =====================================================
         FORMULAIRE
    ====================================================== -->
    <form action="/profils/editprofils" method="post" class="profile-edit-form" enctype="multipart/form-data">
        <?= csrf_field(); ?>
        <div class="profile-edit-layout">
            <!-- =================================================
                 COLONNE PRINCIPALE
            ================================================== -->
            <main class="profile-edit-main">
                <!-- =================================================
                     IDENTITÉ
                ================================================== -->
                <section class="profile-edit-card">
                    <div class="profile-edit-card-header">
                        <div class="profile-edit-card-title">
                            <span class="profile-edit-card-icon">
                                <i class="fa-solid fa-id-card"></i>
                            </span>
                            <div>
                                <h2>Informations personnelles</h2>
                                <p>Les informations principales de votre profil.</p>
                            </div>
                        </div>
                    </div>
                    <div class="profile-edit-card-body">
                        <div class="profile-edit-grid">
                            <!-- USERNAME -->
                            <div class="profile-edit-field">
                                <label for="username">Nom d'utilisateur</label>
                                <div class="profile-edit-input">
                                    <i class="fa-solid fa-user"></i>
                                    <input type="text" id="username" name="username" value="<?= $e($username); ?>" maxlength="100" autocomplete="username" required>
                                </div>
                            </div>
                            <!-- NOM -->
                            <div class="profile-edit-field">
                                <label for="last_name">Nom</label>
                                <div class="profile-edit-input">
                                    <i class="fa-solid fa-user-tag"></i>
                                    <input type="text" id="last_name" name="last_name" value="<?= $e($lastName); ?>" maxlength="100">
                                </div>
                            </div>
                            <!-- GENRE -->
                            <div class="profile-edit-field">
                                <label for="gender">Genre</label>
                                <div class="profile-edit-input">
                                    <i class="fa-solid fa-venus-mars"></i>
                                    <select id="gender" name="gender">
                                        <option value="">
                                            Non renseigné
                                        </option>
                                        <option
                                            value="male"
                                            <?= $gender === 'male'
                                                ? 'selected'
                                                : ''; ?>
                                        >
                                            Homme
                                        </option>
                                        <option
                                            value="female"
                                            <?= $gender === 'female'
                                                ? 'selected'
                                                : ''; ?>
                                        >
                                            Femme
                                        </option>
                                        <option
                                            value="other"
                                            <?= $gender === 'other'
                                                ? 'selected'
                                                : ''; ?>
                                        >
                                            Autre
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <!-- PAYS -->
                            <div class="profile-edit-field">
                                <label for="country">
                                    Pays
                                </label>
                                <div class="profile-edit-input">
                                    <i class="fa-solid fa-earth-europe"></i>
                                    <input type="text" id="country" name="country" value="<?= $e($country); ?>" maxlength="100">
                                </div>
                            </div>
                            <!-- DATE DE NAISSANCE -->
                            <div class="profile-edit-field">
                                <label for="birthday">Date de naissance</label>
                                <div class="profile-edit-input">
                                    <i class="fa-solid fa-cake-candles"></i>
                                    <input type="date" id="birthday" name="birthday" value="<?= $e($birthday); ?>">
                                </div>
                            </div>
                            <!-- TELEPHONE -->
                            <div class="profile-edit-field">
                                <label for="phone">
                                    Téléphone
                                </label>
                                <div class="profile-edit-input">
                                    <i class="fa-solid fa-phone"></i>
                                    <input type="tel" id="phone" name="phone" value="<?= $e($phone); ?>" maxlength="50">
                                </div>
                            </div>
                            <!-- EMAIL -->
                            <div class="profile-edit-field">
                                <label for="public_mail">E-mail public</label>
                                <div class="profile-edit-input">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input type="email" id="public_mail" name="public_mail" value="<?= $e($email); ?>" maxlength="255">
                                </div>
                            </div>
                            <!-- PROFIL -->
                            <div class="profile-edit-field">
                                <label for="profils">Type de profil</label>
                                <div class="profile-edit-input">
                                    <i class="fa-solid fa-layer-group"></i>
                                    <input type="text" id="profils" name="profils" value="<?= $e($profil); ?>" maxlength="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- =================================================
                     PRÉSENTATION
                ================================================== -->
                <section class="profile-edit-card">
                    <div class="profile-edit-card-header">
                        <div class="profile-edit-card-title">
                            <span class="profile-edit-card-icon">
                                <i class="fa-solid fa-comment"></i>
                            </span>
                            <div>
                                <h2>Présentation</h2>
                                <p>Présentez-vous aux autres membres.</p>
                            </div>
                        </div>
                    </div>
                    <div class="profile-edit-card-body">
                        <div class="profile-edit-field">
                            <label for="infos_text">À propos de vous</label>
                            <textarea id="infos_text" name="infos_text" rows="8" maxlength="5000" placeholder="Présentez-vous..."><?= $e($infos); ?></textarea>
                            <div class="profile-edit-help">
                                Vous pouvez utiliser du texte simple.
                            </div>
                        </div>
                    </div>
                </section>
                <!-- =================================================
                     SITE WEB
                ================================================== -->
                <section class="profile-edit-card">
                    <div class="profile-edit-card-header">
                        <div class="profile-edit-card-title">
                            <span class="profile-edit-card-icon">
                                <i class="fa-solid fa-globe"></i>
                            </span>
                            <div>
                                <h2>Site internet</h2>
                                <p>Ajoutez votre site personnel.</p>
                            </div>
                        </div>
                    </div>
                    <div class="profile-edit-card-body">
                        <div class="profile-edit-field">
                            <label for="websites">Adresse du site</label>
                            <div class="profile-edit-input">
                                <i class="fa-solid fa-link"></i>
                                <input type="url" id="websites" name="websites" value="<?= $e($website); ?>" maxlength="255" placeholder="https://exemple.be">
                            </div>
                        </div>
                    </div>
                </section>
                <!-- =================================================
                     BOUTONS
                ================================================== -->
                <div class="profile-edit-actions">
                    <a href="/profile" class="profile-edit-button profile-edit-button-secondary">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                    <button type="submit" class="profile-edit-button profile-edit-button-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer les modifications
                    </button>
                </div>
            </main>
            <!-- =================================================
                 SIDEBAR
            ================================================== -->
            <aside class="profile-edit-sidebar">
                <!-- =================================================
                     AVATAR
                ================================================== -->
                <section class="profile-edit-card">
                    <div class="profile-edit-card-header">
                        <div class="profile-edit-card-title">
                            <span class="profile-edit-card-icon">
                                <i class="fa-solid fa-image"></i>
                            </span>
                            <div>
                                <h2>Apparence</h2>
                                <p>Avatar et couverture.</p>
                            </div>
                        </div>
                    </div>
                    <div class="profile-edit-card-body">
                        <!-- AVATAR -->
                        <div class="profile-edit-media">
                            <div class="profile-edit-media-preview avatar">
                                <img
                                    src="<?= $e(
                                        $avatar !== ''
                                            ? $avatar
                                            : '/assets/images/default-avatar.png'
                                    ); ?>"
                                    alt="Avatar" >
                            </div>
                            <div class="profile-edit-media-info">
                                <strong>Avatar</strong>
                                <span>Image de profil</span>
                            </div>
                        </div>
                        <div class="profile-edit-file">

                            <label for="avatar">
                                <i class="fa-solid fa-upload"></i>
                                Modifier l'avatar
                            </label>
                            <input type="file" id="avatar" name="avatar" accept="image/*,image/webp">
                        </div>
                        <!-- COVER -->
                        <div class="profile-edit-cover-preview">
                            <?php if ($cover !== ''): ?>
                                <img src="<?= $e($cover); ?>" alt="Couverture">
                            <?php else: ?>
                                <div class="profile-edit-cover-empty">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="profile-edit-file">

                            <label for="cover_avatar">
                                <i class="fa-solid fa-upload"></i>
                                Modifier la couverture
                            </label>
                            <input type="file" id="cover_avatar" name="cover_avatar" accept="image/jpeg,image/png,image/webp">
                        </div>
                    </div>
                </section>
                <!-- =================================================
                     GRAVATAR
                ================================================== -->
                <section class="profile-edit-card">
                    <div class="profile-edit-card-header">
                        <div class="profile-edit-card-title">
                            <span class="profile-edit-card-icon">
                                <i class="fa-solid fa-robot"></i>
                            </span>
                            <div>
                                <h2>Gravatar</h2>
                                <p>Avatar externe.</p>
                            </div>
                        </div>
                    </div>
                    <div class="profile-edit-card-body">
                        <div class="profile-edit-field">
                            <label for="gravatar">Adresse Gravatar</label>

                            <div class="profile-edit-input">
                                <i class="fa-solid fa-link"></i>
                                <input type="url" id="gravatar" name="gravatar" value="<?= $e($gravatar); ?>" maxlength="255" placeholder="https://...">
                            </div>
                        </div>
                    </div>
                </section>
                <!-- =================================================
                     INFORMATION
                ================================================== -->
                <div class="profile-edit-information">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div>
                        <strong>Vos données</strong>
                        <p>
                            Les informations publiques peuvent être
                            consultées par les autres membres.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</div>