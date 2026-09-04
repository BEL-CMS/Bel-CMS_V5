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
<div class="belcms_module_news">
    <header class="belcms_module_news_header">
        <h1><?= __('NEWS_TITLE') ?></h1>
        <p>Les dernières actualités de Bel-CMS.</p>
    </header>
    <?php if (!empty($categories)): ?>
        <nav class="belcms_module_news_categories">
            <a href="/news" class="belcms_module_news_category active">Toutes</a>
            <?php foreach ($categories as $category): ?>
                <a href="/news/categorie/<?= urlencode($category->value) ?>" class="belcms_module_news_category">
                    <?= htmlspecialchars($category->value) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
    <?php if (empty($news)): ?>
        <div class="belcms_module_news_empty">
            <?= __('NEWS_NO_RESULT') ?>
        </div>
    <?php else: ?>
        <div class="belcms_module_news_list">
            <?php foreach ($news as $article): ?>
                <article class="belcms_module_news_card">
                    <?php if (!empty($article->img) && $article->img !== '/uploads/news/UPLOAD_NONE'): ?>
                        <div class="belcms_module_news_image">
                            <img src="<?= htmlspecialchars($article->img) ?>" alt="<?= htmlspecialchars($article->name) ?>">
                        </div>
                    <?php endif; ?>

                    <div class="belcms_module_news_content">
                        <h2><?= htmlspecialchars($article->name) ?></h2>
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
                        <div class="belcms_module_news_excerpt">
                            <?= htmlspecialchars($article->excerpt) ?>
                        </div>
                        <a href="/news/<?= urlencode($article->rewrite_name) ?>" class="belcms_module_news_link"><?= __('NEWS_READ_MORE') ?> →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($pagination) && $pagination->totalPages() > 1): ?>
        <?= $pagination->render('/news') ?>
    <?php endif; ?>
</div>