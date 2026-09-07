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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=htmlspecialchars($title);?> - <?=htmlspecialchars($moduleName);?></title>
    <link rel="stylesheet" href="/themes/default/css/style.css">

<?= $this->assets->renderCss() ?>
</head>
<body>
<header class="site-header">
    <div class="site-container">
        <a href="/" class="site-logo">Bel-CMS</a>
        <nav class="site-nav">
            <a href="/">Accueil</a>
            <a href="/news">News</a>
        </nav>
    </div>
</header>
<main class="site-main site-container">
    <?php if ($moduleName !== null): ?>

        <div class="current-module">
            <?= htmlspecialchars($moduleName) ?>
        </div>

    <?php endif; ?>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="site-container">© Bel-CMS V5 @ <?= date('Y'); ?></div>
</footer>

<?= $this->assets->renderJs() ?>
</body>
</html>
