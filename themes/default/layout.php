<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>

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

<main class="site-main">
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="site-container">Bel-CMS V5</div>
</footer>

<?= $this->assets->renderJs() ?>
</body>
</html>
