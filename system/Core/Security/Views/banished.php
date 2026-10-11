<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/

declare(strict_types=1);

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

/**
 * Bel-CMS V5
 * Page affichée aux utilisateurs bannis.
 *
 * Variables attendues :
 * $reason       : motif du bannissement
 * $expiresAt    : date d'expiration ou null si permanent
 * $banishmentKey : référence de la sanction sur 32 caractères
 */

$reason = isset($reason) && is_string($reason)
    ? $reason
    : 'Aucun motif communiqué.';

$expiresAt = isset($expiresAt) && is_string($expiresAt)
    ? $expiresAt
    : null;

$banishmentKey = isset($banishmentKey) && is_string($banishmentKey)
    ? trim($banishmentKey)
    : null;

if ($banishmentKey !== null && !preg_match('/^[a-f0-9]{32}$/i', $banishmentKey)) {
    $banishmentKey = null;
}

$escape = static fn (mixed $value): string =>
    htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

$isPermanent = $expiresAt === null || $expiresAt === '';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#080d18">

    <title>Accès suspendu - Bel-CMS</title>

    <link
        rel="stylesheet"
        href="/system/Core/Security/Views/banished.css"
    >
</head>

<body>

<div class="banished-background" aria-hidden="true">
    <div class="background-grid"></div>
    <div class="background-glow background-glow-one"></div>
    <div class="background-glow background-glow-two"></div>
</div>

<main class="banished-container">

    <section class="banished-card" aria-labelledby="banished-title">

        <div class="brand">
            <span class="brand-icon" aria-hidden="true">B</span>
            <span class="brand-name">Bel<span>-</span>CMS</span>
            <span class="brand-version">V5</span>
        </div>

        <div class="security-icon" aria-hidden="true">
            <svg
                viewBox="0 0 64 64"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M32 5L53 13V29C53 43 44 53 32 59C20 53 11 43 11 29V13L32 5Z"
                    stroke="currentColor"
                    stroke-width="3"
                    stroke-linejoin="round"
                />
                <path
                    d="M32 20V34"
                    stroke="currentColor"
                    stroke-width="4"
                    stroke-linecap="round"
                />
                <circle cx="32" cy="43" r="2.5" fill="currentColor"/>
            </svg>
        </div>

        <div class="status-label">
            <span class="status-dot"></span>
            RESTRICTION D'ACCÈS
        </div>

        <h1 id="banished-title">
            Accès <span>suspendu</span>
        </h1>

        <p class="banished-description">
            L'accès à ce site a été restreint à la suite d'une sanction.
            Vous ne pouvez actuellement pas accéder aux pages protégées
            de Bel-CMS.
        </p>

        <div class="details-panel">

            <div class="panel-heading">
                <span class="panel-heading-icon" aria-hidden="true">
                    &#9432;
                </span>
                Détails de la sanction
            </div>

            <div class="detail-row">
                <div class="detail-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path
                            d="M4 5H20V17H13L8 21V17H4V5Z"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M8 9H16M8 13H14"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <div class="detail-content">
                    <span class="detail-label">Motif</span>
                    <p><?= $escape($reason) ?></p>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke="currentColor"
                            stroke-width="1.7"
                        />
                        <path
                            d="M12 7V12L15 14"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>

                <div class="detail-content">
                    <span class="detail-label">Durée</span>

                    <?php if ($isPermanent): ?>
                        <p class="permanent-text">
                            Bannissement permanent
                        </p>
                    <?php else: ?>
                        <p>
                            Jusqu'au <?= $escape($expiresAt) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($banishmentKey !== null): ?>
                <div class="detail-row">
                    <div class="detail-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M8 5H5V19H19V5H16"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M9 3H15V7H9V3Z"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M9 12H15M9 16H13"
                                stroke="currentColor"
                                stroke-width="1.7"
                                stroke-linecap="round"
                            />
                        </svg>
                    </div>

                    <div class="detail-content">
                        <span class="detail-label">Référence de votre bannissement</span>
                        <p class="reference-value">
                            <?= $escape($banishmentKey) ?>
                        </p>
                        <p class="reference-help">
                            Communiquez cette référence à l'administration pour retrouver rapidement votre dossier.
                        </p>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <div class="notice">
            <span class="notice-icon" aria-hidden="true">!</span>
            <p>
                Si vous pensez qu'il s'agit d'une erreur, veuillez
                contacter l'équipe d'administration du site.
            </p>
        </div>

        <footer class="banished-footer">
            <span class="footer-line"></span>
            <span>Bel-CMS Security</span>
            <span class="footer-separator">/</span>
            <span>Protection des accès</span>
            <span class="footer-line"></span>
        </footer>

    </section>

    <p class="copyright">
        &copy; <?= date('Y') ?> Bel-CMS. Tous droits réservés.
    </p>

</main>

</body>
</html>
