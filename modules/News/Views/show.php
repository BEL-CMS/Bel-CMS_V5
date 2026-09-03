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

<article class="news-single">
    <header class="news-header">
        <h1><?= htmlspecialchars($article->name) ?></h1>

        <div class="news-meta">
            <span><?= htmlspecialchars($article->date_create) ?></span>

            <?php if (!empty($article->author)): ?>
                <span>Par <?= htmlspecialchars($article->author) ?></span>
            <?php endif; ?>

            <span><?= (int) $article->view ?> vues</span>

            <?php if (isset($article->like_post)): ?>
                <span><?= (int) $article->like_post ?> likes</span>
            <?php endif; ?>
        </div>
    </header>

    <?php if (!empty($article->img) && $article->img !== '/uploads/news/UPLOAD_NONE'): ?>
        <div class="news-image">
            <img
                src="<?= htmlspecialchars($article->img) ?>"
                alt="<?= htmlspecialchars($article->name) ?>"
            >
        </div>
    <?php endif; ?>

    <div class="news-article-content">
        <?= $article->content ?>
    </div>

    <?php if (!empty($article->additionalcontent)): ?>
        <div class="news-additional-content">
            <?= $article->additionalcontent ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($article->tags)): ?>
        <div class="news-tags">
            <strong>Tags :</strong>
            <?= htmlspecialchars($article->tags) ?>
        </div>
    <?php endif; ?>

    <div class="news-footer">
        <a href="/news" class="news-link">← Retour aux actualités</a>
    </div>
</article>
