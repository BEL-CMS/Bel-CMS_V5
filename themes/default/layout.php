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
$currentUser = null;
if (
    isset($GLOBALS['belcms_app']) &&
    is_object($GLOBALS['belcms_app'])
) {
    try {
        $currentUser = $GLOBALS['belcms_app']->user()->current();
    } catch (\Throwable) {
        $currentUser = null;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bel-CMS V5 - Content Management System moderne, puissant et flexible.">
    <meta name="generator" content="Bel-CMS V5">
    <title><?= htmlspecialchars($title ?? 'Bel-CMS', ENT_QUOTES, 'UTF-8'); ?>- Bel-CMS V5
    </title>
    <link rel="stylesheet" href="/themes/default/css/style.css">
    <?= $this->assets->renderCss(); ?>
</head>
<body class="bc-body">
    <!-- =========================
         HEADER
    ========================== -->
    <header class="bc-header">
        <div class="bc-container bc-header-inner">
            <a href="/" class="bc-logo">
                <span class="bc-logo-mark">
                    <i class="fa-solid fa-cube"></i>
                </span>
                <span class="bc-logo-text">Bel-CMS</span>
            </a>
            <nav class="bc-nav">
                <a href="/">
                    <i class="fa-solid fa-house"></i>
                    <span>Accueil</span>
                </a>
                <a href="/news">
                    <i class="fa-solid fa-newspaper"></i>
                    <span>News</span>
                </a>
                <?php if ($currentUser !== null): ?>
                    <a href="/user">
                        <i class="fa-solid fa-user"></i>
                        <span>Mon compte</span>
                    </a>
                    <a href="/user/security">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Sécurité</span>
                    </a>
                    <a href="/user/logout" class="bc-nav-logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Déconnexion</span>
                    </a>
                <?php else: ?>
                    <a href="/user/login" class="bc-nav-login">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Connexion</span>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <!-- =========================
         MAIN
    ========================== -->
    <main class="bc-main">
        <div class="bc-container">
            <?= $content ?>
        </div>
    </main>
    <!-- =========================
         FOOTER
    ========================== -->
    <footer class="bc-footer">
        <div class="bc-container">
            <div class="bc-footer-grid">
                <div class="bc-footer-column">
                    <div class="bc-footer-brand">
                        <span class="bc-logo-mark">
                            <i class="fa-solid fa-cube"></i>
                        </span>
                        <span>Bel-CMS</span>
                    </div>
                    <p>
                        Un CMS moderne, flexible et sécurisé,
                        conçu pour créer des sites web performants.
                    </p>
                </div>


                <div class="bc-footer-column">
                    <h3>Navigation</h3>
                    <a href="/">
                        Accueil
                    </a>
                    <a href="/news">
                        News
                    </a>
                    <?php if ($currentUser !== null): ?>
                        <a href="/user">
                            Mon compte
                        </a>
                    <?php endif; ?>
                </div>
                <div class="bc-footer-column">
                    <h3>Compte</h3>
                    <?php if ($currentUser !== null): ?>
                        <a href="/user/security">Sécurité</a>
                        <a href="/user/password">Mot de passe</a>
                    <?php else: ?>
                        <a href="/user/login">Connexion</a>
                    <?php endif; ?>
                </div>
                <div class="bc-footer-column">
                    <h3>Projet</h3>
                    <a href="https://www.bel-cms.dev" target="_blank" rel="noopener">Site officiel</a>
                    <a href="#">Documentation</a>
                    <a href="#">GitHub</a>
                </div>
            </div>
            <div class="bc-footer-bottom">
                <span>
                    © Bel-CMS V5 @ <?= date('Y'); ?>
                </span>
                <span>
                    Développé avec PHP <?= PHP_VERSION; ?>
                </span>
            </div>
        </div>
    </footer>
    <?= $this->assets->renderJs(); ?>
</body>
</html>