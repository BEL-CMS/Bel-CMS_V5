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
<div class="belcms_module_news_page">
    <?php if ($news === null): ?>
        <article class="belcms_module_news_single">
            <header class="belcms_module_news_header">
                <h1>Actualité introuvable</h1>
                <p>L'actualité demandée n'existe pas ou n'est plus disponible.</p>
            </header>

            <div class="belcms_module_news_empty">
                <a href="/news" class="belcms_module_news_link">← Retour aux actualités</a>
            </div>
        </article>
    <?php else: ?>
        <?php
        $title = (string) ($news->title ?? '');
        $image = (string) ($news->image ?? '');
        ?>

        <article class="belcms_module_news_single">
            <header class="belcms_module_news_header">
                <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>

                <div class="belcms_module_news_meta">
                    <span>
                        <i class="fa-regular fa-calendar"></i>
                        <?= htmlspecialchars((string) ($news->created_at ?? ''), ENT_QUOTES, 'UTF-8') ?>
                    </span>

                    <?php if ($category !== null): ?>
                        <span>
                            <i class="fa-regular fa-folder"></i>
                            <a href="/news/category?id_cat=<?= (int) $category->id_cat ?>">
                                <?= htmlspecialchars((string) $category->name, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </span>
                    <?php endif; ?>

                    <span>
                        <i class="fa-regular fa-eye"></i>
                        <?= (int) ($news->views ?? 0) ?> vues
                    </span>

                    <span>
                        <i class="fa-regular fa-heart"></i>
                        <?= (int) ($news->likes ?? 0) ?> likes
                    </span>
                </div>
            </header>

            <?php if ($image !== ''): ?>
                <div class="belcms_module_news_image belcms_module_news_image_single">
                    <img
                        src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>
            <?php endif; ?>

            <div class="belcms_module_news_article_content">
                <?= (string) ($news->content ?? '') ?>
            </div>

            <?php if (!empty($news->tags)): ?>
                <div class="belcms_module_news_tags">
                    <strong>Tags :</strong>
                    <?= htmlspecialchars((string) $news->tags, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div class="belcms_module_news_footer">
                <a href="/news" class="belcms_module_news_link">← Retour aux actualités</a>
            </div>
        </article>
    <?php endif; ?>
</div>
