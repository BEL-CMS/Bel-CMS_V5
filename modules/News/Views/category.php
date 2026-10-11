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

$categoryName = $category !== null
    ? (string) ($category->name ?? '')
    : 'Catégorie introuvable';
?>
<div class="belcms_module_news_page">
    <header class="belcms_module_news_header">
        <h1>Actualités : <?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars((string) ($category->description ?? 'Les actualités de cette catégorie.'), ENT_QUOTES, 'UTF-8') ?></p>
    </header>

    <?php if ($category === null): ?>
        <div class="belcms_module_news_empty">
            Cette catégorie n'existe pas.
        </div>
    <?php elseif (empty($news)): ?>
        <div class="belcms_module_news_empty">
            Aucune actualité dans cette catégorie.
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
