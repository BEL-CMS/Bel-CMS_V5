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
        <a class="admin-logo" href="/admin" aria-label="Bel-CMS Administration">
            <span class="admin-logo-wordmark">Bel-<span>CMS</span></span>
        </a>
        <nav class="admin-navigation" aria-label="Navigation d’administration">
            <a href="/admin" class="admin-nav-link admin-nav-home<?= ($currentPath === '/admin' || $currentPath === '/admin/') ? ' active' : '' ?>">
                <span class="admin-nav-label"><i class="fa-solid fa-house"></i><span>Accueil</span></span>
            </a>

            <div class="admin-nav-group<?= (str_starts_with($currentPath, '/admin/settings') || str_starts_with($currentPath, '/admin/security') || str_starts_with($currentPath, '/admin/logs')) ? ' is-open' : '' ?>">
                <button type="button" class="admin-nav-group-toggle" aria-expanded="false">
                    <span class="admin-nav-label"><i class="fa-solid fa-sliders"></i><span>Paramètres</span></span>
                    <span class="admin-nav-plus" aria-hidden="true"><?= (str_starts_with($currentPath, '/admin/settings') || str_starts_with($currentPath, '/admin/security') || str_starts_with($currentPath, '/admin/logs')) ? '−' : '+' ?></span>
                </button>
                <div class="admin-nav-submenu">
                    <a href="/admin/settings" class="admin-nav-sublink<?= str_starts_with($currentPath, '/admin/settings') ? ' active' : '' ?>">Configuration</a>
                    <a href="/admin/security" class="admin-nav-sublink<?= str_starts_with($currentPath, '/admin/security') ? ' active' : '' ?>">Sécurité</a>
                    <a href="/admin/logs" class="admin-nav-sublink<?= str_starts_with($currentPath, '/admin/logs') ? ' active' : '' ?>">Journaux</a>
                </div>
            </div>

            <?php
            /*
             * Les modules peuvent définir une catégorie via "category" ou "group".
             * Sinon, Bel-CMS tente de les ranger selon leur nom/label.
             */
            $menuGroups = [
                'Pages' => [],
                'Utilisateurs' => [],
                'Widgets' => [],
                'Extras' => [],
                'Teams' => [],
                'ASEH' => [],
                'Modules' => [],
            ];

            foreach ($moduleMenuItems as $item) {
                $moduleName = strtolower((string) ($item['module'] ?? ''));
                $moduleLabel = strtolower((string) ($item['label'] ?? $item['module'] ?? ''));
                $category = trim((string) ($item['category'] ?? $item['group'] ?? ''));

                if ($category === '') {
                    $haystack = $moduleName . ' ' . $moduleLabel;
                    if (str_contains($haystack, 'page') || str_contains($haystack, 'article') || str_contains($haystack, 'news')) {
                        $category = 'Pages';
                    } elseif (str_contains($haystack, 'user') || str_contains($haystack, 'member') || str_contains($haystack, 'utilisateur') || str_contains($haystack, 'banish') || str_contains($haystack, 'bannissement') || str_contains($haystack, 'banissement')) {
                        $category = 'Utilisateurs';
                    } elseif (str_contains($haystack, 'widget')) {
                        $category = 'Widgets';
                    } elseif (str_contains($haystack, 'team') || str_contains($haystack, 'equipe')) {
                        $category = 'Teams';
                    } else {
                        $category = 'Extras';
                    }
                }

                $categoryKey = array_key_exists($category, $menuGroups) ? $category : 'Modules';
                $menuGroups[$categoryKey][] = $item;
            }

            foreach ($menuGroups as $groupName => $items):
                if (empty($items)) {
                    continue;
                }
                $groupOpen = false;
                foreach ($items as $groupItem) {
                    $route = (string) ($groupItem['route'] ?? '');
                    if ($route !== '' && ($currentPath === $route || str_starts_with($currentPath, rtrim($route, '/') . '/'))) {
                        $groupOpen = true;
                        break;
                    }
                }
            ?>
                <div class="admin-nav-group<?= $groupOpen ? ' is-open' : '' ?>">
                    <button type="button" class="admin-nav-group-toggle" aria-expanded="<?= $groupOpen ? 'true' : 'false' ?>">
                        <span class="admin-nav-label"><i class="fa-solid <?= $groupName === 'Pages' ? 'fa-file-lines' : ($groupName === 'Utilisateurs' ? 'fa-users' : ($groupName === 'Widgets' ? 'fa-puzzle-piece' : ($groupName === 'Teams' ? 'fa-people-group' : ($groupName === 'ASEH' ? 'fa-water' : 'fa-folder-open'))) ) ?>"></i><span><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?></span></span>
                        <span class="admin-nav-plus" aria-hidden="true"><?= $groupOpen ? '−' : '+' ?></span>
                    </button>
                    <div class="admin-nav-submenu">
                        <?php foreach ($items as $item): ?>
                            <?php
                            $moduleRoute = (string) ($item['route'] ?? '/admin/' . strtolower((string) ($item['module'] ?? '')));
                            $moduleActive = $currentModule !== null && strcasecmp((string) ($currentModule['module'] ?? ''), (string) ($item['module'] ?? '')) === 0;
                            ?>
                            <a href="<?= htmlspecialchars($moduleRoute, ENT_QUOTES, 'UTF-8') ?>" class="admin-nav-sublink<?= $moduleActive ? ' active' : '' ?>">
                                <?= htmlspecialchars((string) ($item['label'] ?? $item['module'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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

        document.querySelectorAll('.admin-nav-group-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                const group = button.closest('.admin-nav-group');
                if (!group) {
                    return;
                }

                const isOpen = group.classList.toggle('is-open');
                button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

                const indicator = button.querySelector('.admin-nav-plus');
                if (indicator) {
                    indicator.textContent = isOpen ? '−' : '+';
                }
            });
        });
    }
);
</script>
</body>
</html>