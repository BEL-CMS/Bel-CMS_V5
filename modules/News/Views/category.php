<?php

declare(strict_types=1);
?>

<div class="news-page">
    <header class="news-header">
        <h1>Actualités : <?= htmlspecialchars($categoryName) ?></h1>
        <p>Les actualités de cette catégorie.</p>
    </header>

    <?php if (empty($news)): ?>
        <div class="news-empty">Aucune actualité dans cette catégorie.</div>
    <?php else: ?>
        <div class="news-list">
            <?php foreach ($news as $article): ?>
                <article class="news-card">
                    <?php if (!empty($article->img) && $article->img !== '/uploads/news/UPLOAD_NONE'): ?>
                        <div class="news-image">
                            <img src="<?= htmlspecialchars($article->img) ?>" alt="<?= htmlspecialchars($article->name) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="news-content">
                        <h2><?= htmlspecialchars($article->name) ?></h2>
                        <div class="news-meta">
                            <span><?= htmlspecialchars($article->date_create) ?></span>
                            <span><?= (int) $article->view ?> vues</span>
                        </div>
                        <div class="news-excerpt"><?= htmlspecialchars($article->excerpt) ?></div>
                        <a href="/news/<?= urlencode($article->rewrite_name) ?>" class="news-link">Lire la suite</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
