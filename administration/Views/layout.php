<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */

declare(strict_types=1);

$title = $title ?? 'Administration';
$content = $content ?? '';
$moduleMenuItems = [];
if (isset($adminMenu)) {
    $moduleMenuItems = $adminMenu->getItems();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> - Bel-CMS</title>
    <?= $this->assets->renderCss() ?>
    <link rel="stylesheet" href="/administration/css/administration.css">
</head>
<body class="admin-body">
<div class="admin-wrapper">
    <!-- =========================================================
         SIDEBAR
         ========================================================= -->
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <div class="admin-logo-icon">
                <i class="fa-solid fa-cube"></i>
            </div>
            <div class="admin-logo-text">
                <strong>Bel-CMS</strong>
                <span>Administration</span>
            </div>
        </div>
        <nav class="admin-navigation">
            <div class="admin-nav-title">
                GÉNÉRAL
            </div>
            <a href="/admin"
               class="admin-nav-link active">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Tableau de bord</span>
            </a>
            <div class="admin-nav-title">
                GESTION
            </div>
            <a href="/admin/users"
               class="admin-nav-link">
                <i class="fa-solid fa-users"></i>
                <span>Utilisateurs</span>
            </a>
            <a href="/admin/groups"
               class="admin-nav-link">
                <i class="fa-solid fa-user-group"></i>
                <span>Groupes</span>
            </a>
            <a href="/admin/modules"
               class="admin-nav-link">
                <i class="fa-solid fa-puzzle-piece"></i>
                <span>Modules</span>
            </a>
            <?php if (!empty($moduleMenuItems)): ?>
                <div class="admin-nav-title">
                    MODULES
                </div>
                <?php foreach ($moduleMenuItems as $item): ?>
                    <a
                        href="/admin/<?= htmlspecialchars(
                            strtolower((string) $item['module']),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="admin-nav-link">
                        <i class="fa-solid <?= htmlspecialchars(
                            (string) $item['icon'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"></i>
                        <span>
                            <?= htmlspecialchars(
                                (string) $item['label'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="admin-nav-title">
                SYSTÈME
            </div>
            <a href="/admin/security"
               class="admin-nav-link">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Sécurité</span>
            </a>
            <a href="/admin/settings"
               class="admin-nav-link">
                <i class="fa-solid fa-gears"></i>
                <span>Configuration</span>
            </a>
            <a href="/admin/logs"
               class="admin-nav-link">
                <i class="fa-solid fa-file-lines"></i>
                <span>Journaux</span>
            </a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="/"
               class="admin-footer-link">
                <i class="fa-solid fa-globe"></i>
                <span>Retour au site</span>
            </a>
            <a href="/user/logout"
               class="admin-footer-link admin-logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>
    <!-- =========================================================
         CONTENU
         ========================================================= -->
    <main class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <button type="button" class="admin-menu-toggle" id="adminMenuToggle" aria-label="Ouvrir le menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <div class="admin-page-title">
                        Administration
                    </div>
                    <div class="admin-page-subtitle">
                        Centre d'administration de Bel-CMS
                    </div>
                </div>
            </div>
            <div class="admin-topbar-right">
                <a href="/" class="admin-topbar-link" title="Voir le site">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
                <a href="/user/profile"
                   class="admin-user">
                    <span class="admin-user-icon">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <span class="admin-user-name">
                        <?= htmlspecialchars(
                            (string)($user->username ?? 'Administrateur'),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </a>
            </div>
        </header>
        <section class="admin-content">
            <?= $content ?>
        </section>
        <footer class="admin-footer">
            <span>
                Bel-CMS V5
            </span>
            <span>
                Administration
            </span>
        </footer>
    </main>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('adminMenuToggle');
    const body = document.body;
    if (toggle) {
        toggle.addEventListener('click', function () {
            body.classList.toggle('admin-sidebar-open');
        });
    }
});
</script>
<?= $this->assets->renderJs() ?>
</body>
</html>