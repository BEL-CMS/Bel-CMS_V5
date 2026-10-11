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
    <header class="belcms_module_news_header">
        <h1><?= __('NEWS_TITLE') ?></h1>
        <p>Les dernières actualités de Bel-CMS.</p>
    </header>

    <?php if (!empty($categories)): ?>
        <nav class="belcms_module_news_categories" aria-label="Catégories des actualités">
            <a href="/news" class="belcms_module_news_category active">Toutes</a>

            <?php foreach ($categories as $category): ?>
                <a
                    href="/news/category?id_cat=<?= (int) $category->id_cat ?>"
                    class="belcms_module_news_category"
                >
                    <?= htmlspecialchars((string) $category->name, ENT_QUOTES, 'UTF-8') ?>
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
                <?php
                $title = (string) ($article->title ?? '');
                $slug = (string) ($article->slug ?? '');
                $image = (string) ($article->image ?? '');
                $content = trim(strip_tags((string) ($article->content ?? '')));
                $excerpt = function_exists('mb_substr')
                    ? mb_substr($content, 0, 180, 'UTF-8')
                    : substr($content, 0, 180);
                ?>

                <article class="belcms_module_news_card">
                    <?php if ($image !== ''): ?>
                        <div class="belcms_module_news_image">
                            <img
                                src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                                loading="lazy"
                            >
                        </div>
                    <?php endif; ?>

                    <div class="belcms_module_news_content">
                        <h2>
                            <a href="/news/view?slug=<?= urlencode($slug) ?>">
                                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h2>

                        <div class="belcms_module_news_meta">
                            <span>
                                <i class="fa-regular fa-calendar"></i>
                                <?= htmlspecialchars((string) ($article->created_at ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span>
                                <i class="fa-regular fa-eye"></i>
                                <?= (int) ($article->views ?? 0) ?> vues
                            </span>
                            <span>
                                <i class="fa-regular fa-heart"></i>
                                <?= (int) ($article->likes ?? 0) ?> likes
                            </span>
                        </div>

                        <?php if ($excerpt !== ''): ?>
                            <div class="belcms_module_news_excerpt">
                                <?= htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($content, 'UTF-8') > 180 ? '…' : '' ?>
                            </div>
                        <?php endif; ?>

                        <a
                            href="/news/view?slug=<?= urlencode($slug) ?>"
                            class="belcms_module_news_link"
                        >
                            <?= __('NEWS_READ_MORE') ?> →
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
