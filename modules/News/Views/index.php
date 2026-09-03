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

<div class="news-page">

    <header class="news-header">

        <h1>Actualités</h1>

        <p>
            Les dernières actualités de Bel-CMS.
        </p>

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


    <?php if (empty($news)): ?>

        <div class="news-empty">
            Aucune actualité disponible.
        </div>

    <?php else: ?>

        <div class="news-list">

            <?php foreach ($news as $article): ?>

                <article class="news-card">

                    <?php if (
                        !empty($article->img)
                        && $article->img !== '/uploads/news/UPLOAD_NONE'
                    ): ?>

                        <div class="news-image">

                            <img
                                src="<?= htmlspecialchars($article->img) ?>"
                                alt="<?= htmlspecialchars($article->name) ?>"
                            >

                        </div>

                    <?php endif; ?>


                    <div class="news-content">

                        <h2>
                            <?= htmlspecialchars($article->name) ?>
                        </h2>


                        <div class="news-meta">

                            <span>
                                <?= htmlspecialchars($article->date_create) ?>
                            </span>

                            <?php if (!empty($article->author)): ?>

                                <span>
                                    Par <?= htmlspecialchars($article->author) ?>
                                </span>

                            <?php endif; ?>

                            <span>
                                <?= (int) $article->view ?> vues
                            </span>

                            <?php if (isset($article->like_post)): ?>

                                <span>
                                    <?= (int) $article->like_post ?> likes
                                </span>

                            <?php endif; ?>

                        </div>


                        <div class="news-excerpt">

                            <?= htmlspecialchars($article->excerpt) ?>

                        </div>


                        <a
                            href="/news/<?= urlencode($article->rewrite_name) ?>"
                            class="news-link"
                        >
                            Lire la suite →
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>


    <?php if (
        isset($pagination)
        && $pagination->totalPages() > 1
    ): ?>

        <?= $pagination->render('/news') ?>

    <?php endif; ?>

</div>