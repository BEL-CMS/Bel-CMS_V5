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
<article class="belcms_module_news_single">
    <header class="belcms_module_news_header">
        <h1><?= htmlspecialchars($article->name) ?></h1>

        <div class="belcms_module_news_meta">
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
        <div class="belcms_module_news_image">
            <img src="<?= htmlspecialchars($article->img) ?>" alt="<?= htmlspecialchars($article->name) ?>">
        </div>
    <?php endif; ?>

    <div class="belcms_module_news_article-content">
        <?= $article->content ?>
    </div>

    <?php if (!empty($article->additionalcontent)): ?>
        <div class="belcms_module_news_additional-content">
            <?= $article->additionalcontent ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($article->tags)): ?>
        <div class="belcms_module_news_tags">
            <strong>Tags :</strong>
            <?= htmlspecialchars($article->tags) ?>
        </div>
    <?php endif; ?>

    <div class="belcms_module_news_footer">
        <a href="/news" class="belcms_module_news_link">← Retour aux actualités</a>
    </div>
</article>
