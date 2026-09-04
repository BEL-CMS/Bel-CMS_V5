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

use BelCMS\system\Common;

?>
<div class="forum-page">
    <header class="news-header">
        <h1><?= __('NEWS_TITLE') ?></h1>
        <p>Liste des pages.</p>
    </header>
    <?php if (empty($pages)): ?>
        <div class="news-empty">
            <?= __('NEWS_NO_RESULT') ?>
        </div>
    <?php else: ?>
        <div class="news-list">
            <?php foreach ($pages as $page): ?>
                <article class="news-card">
                    <div class="news-content">
                        <h2><?= htmlspecialchars($page->name) ?></h2>
                        <div class="news-meta">
                            <span><?= Common::TransformDate(htmlspecialchars($page->publish_date), 'MEDIUM', 'MEDIUM'); ?></span>
                            <?php if (!empty($page->author)): ?>
                                <span>Par <?= htmlspecialchars($page->author) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="news-excerpt">
                            <?= $page->description ?>
                        </div>
                        <a href="/pages/<?= urlencode($page->id_page) ?>" class="news-link"><?= __('NEWS_READ_MORE') ?> →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>