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

$title = $title ?? 'Administration';
$content = $content ?? '';
$moduleMenuItems = [];
$currentModule = null;
$currentModuleMenu = [];
$currentPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);
if (!is_string($currentPath)) {
    $currentPath = '/';
}
/*
 * Menu des modules
 */
if (isset($adminMenu)) {
    $moduleMenuItems = $adminMenu->getItems();
    /*
     * Détermine le module actuellement ouvert
     */
    $adminMenu->setCurrentPath(
        $currentPath
    );
    $currentModule = $adminMenu->current();
    if (is_array($currentModule)) {
        $currentModuleMenu = $adminMenu->currentMenu();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars(
            $title,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
        - Bel-CMS
    </title>
    <?= $this->assets->renderCss() ?>
    <link rel="stylesheet" href="/administration/css/administration.css">
</head>
<body class="admin-body">
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <div class="admin-logo-icon">
                <i class="fa-solid fa-cube"></i>
            </div>
            <div class="admin-logo-text">
                <strong>
                    Bel-CMS
                </strong>
                <span>
                    Administration
                </span>
            </div>
        </div>
        <nav class="admin-navigation">
            <?php if (!empty($moduleMenuItems)): ?>
                <div class="admin-nav-title">MODULES</div>
                <?php foreach ($moduleMenuItems as $item): ?>
                    <?php
                    /*
                     * Route d'administration du module.
                     *
                     * On utilise la route définie par le module
                     * afin de ne pas déduire l'URL depuis son nom.
                     *
                     * Exemple :
                     * User -> /admin/users
                     */
                    $moduleRoute = (string) (
                        $item['route']
                        ?? '/admin/' . strtolower(
                            (string) (
                                $item['module'] ?? ''
                            )
                        )
                    );
                    $moduleActive = false;
                    if ($currentModule !== null) {
                        $moduleActive =
                            strcasecmp(
                                (string) (
                                    $currentModule['module']
                                    ?? ''
                                ),
                                (string) (
                                    $item['module']
                                    ?? ''
                                )
                            ) === 0;
                    }
                    ?>
                    <a
                        href="<?= htmlspecialchars(
                            $moduleRoute,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        class="admin-nav-link<?= $moduleActive ? ' active' : '' ?>"
                    >
                        <i class="fa-solid <?= htmlspecialchars(
                            (string) (
                                $item['icon']
                                ?? 'fa-puzzle-piece'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"></i>
                        <span>
                            <?= htmlspecialchars(
                                (string) (
                                    $item['label']
                                    ?? $item['module']
                                    ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="admin-nav-title">SYSTÈME</div>
            <a href="/admin/security" class="admin-nav-link">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Sécurité</span>
            </a>
            <a href="/admin/settings" class="admin-nav-link">
                <i class="fa-solid fa-gears"></i>
                <span>Configuration</span>
            </a>
            <a href="/admin/logs" class="admin-nav-link">
                <i class="fa-solid fa-file-lines"></i>
                <span>Journaux</span>
            </a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="/" class="admin-footer-link">
                <i class="fa-solid fa-globe"></i>
                <span>Retour au site</span>
            </a>
            <a href="/user/logout" class="admin-footer-link admin-logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <button type="button" class="admin-menu-toggle" id="adminMenuToggle" aria-label="Ouvrir le menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <div class="admin-page-title">Administration</div>
                    <div class="admin-page-subtitle">Centre d'administration de Bel-CMS</div>
                </div>
            </div>
            <div class="admin-topbar-right">
                <a href="/" class="admin-topbar-link" title="Voir le site">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
                <a href="/user/profile" class="admin-user">
                    <span class="admin-user-icon">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <span class="admin-user-name">
                        <?= htmlspecialchars(
                            (string) (
                                $user->username
                                ?? 'Administrateur'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </a>
            </div>
        </header>
        <?php if (
            $currentModule !== null
            && !empty($currentModuleMenu)
        ): ?>
            <nav class="admin-module-menu">
                <div class="admin-module-menu-title">
                    <i class="fa-solid <?= htmlspecialchars(
                        (string) (
                            $currentModule['icon']
                            ?? 'fa-puzzle-piece'
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"></i>
                    <span>
                        <?= htmlspecialchars(
                            (string) (
                                $currentModule['label']
                                ?? $currentModule['module']
                                ?? ''
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </span>
                </div>
                <div class="admin-module-menu-items">
                    <?php foreach (
                        $currentModuleMenu
                        as $menuItem
                    ): ?>
                        <?php
                        $menuRoute = (string) (
                            $menuItem['route']
                            ?? '#'
                        );
                        $menuActive = $adminMenu->isActive(
                            $menuRoute,
                            $currentPath
                        );
                        ?>
                        <a
                            href="<?= htmlspecialchars($menuRoute,ENT_QUOTES,'UTF-8') ?>" class="admin-module-menu-link<?= $menuActive ? ' active' : '' ?>">
                            <?php if (
                                !empty($menuItem['icon'])
                            ): ?>
                                <i class="fa-solid <?= htmlspecialchars((string) $menuItem['icon'],ENT_QUOTES,'UTF-8') ?>"></i>
                            <?php endif; ?>
                            <span>
                                <?= htmlspecialchars((string) ($menuItem['label']?? ''),ENT_QUOTES,'UTF-8') ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </nav>
        <?php endif; ?>
        <section class="admin-content">
            <?= $content ?>
        </section>
        <footer class="admin-footer">
            <span>Bel-CMS V5</span>
            <span>Administration</span>
        </footer>
    </main>
</div>

<?= $this->assets->renderJs() ?>
<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const toggle =
            document.getElementById(
                'adminMenuToggle'
            );
        const body = document.body;
        if (toggle) {
            toggle.addEventListener(
                'click',
                function () {
                    body.classList.toggle(
                        'admin-sidebar-open'
                    );
                }
            );
        }
    }
);
</script>
</body>
</html>