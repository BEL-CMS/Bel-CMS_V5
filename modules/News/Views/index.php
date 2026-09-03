<?php

declare(strict_types=1);
?>

<div class="news-page">
    <header class="news-header">
        <h1>Actualités</h1>
        <p>Les dernières actualités de Bel-CMS.</p>
    </header>

<?php if (!empty($categories)): ?>

    <nav class="news-categories">

        <a
            href="/news"
            class="news-category active"
        >
            Toutes
        </a>

        <?php foreach ($categories as $category): ?>

            <a
                href="/news/categorie/<?= urlencode($category->value) ?>"
                class="news-category"
            >
                <?= htmlspecialchars($category->value) ?>
            </a>

        <?php endforeach; ?>

    </nav>

<?php endif; ?>
</div>
