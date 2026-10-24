<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */
declare(strict_types=1);

use BelCMS\system\Common;

/** @var array $news */
/** @var array $categories */
/** @var string $csrf */
$news = $news ?? [];
$categories = $categories ?? [];
$csrf = $csrf ?? '';
/*
 * Indexation des catégories
 */
$categoryMap = [];
foreach ($categories as $category) {
    $categoryMap[(int) $category->id_cat] = $category;
}
?>
<div class="admin-news">
    <!-- =========================================================
         HEADER
         ========================================================= -->
    <div class="admin-news-header">
        <div>
            <h1>
                <i class="fa-solid fa-newspaper"></i>
                Actualités
            </h1>
            <p>
                Gestion des actualités de Bel-CMS.
            </p>
        </div>
        <div class="admin-news-actions">
            <a href="/admin/news/categories"
               class="admin-news-button secondary">
                <i class="fa-solid fa-folder"></i>
                Catégories
            </a>
            <a href="/admin/news/create"
               class="admin-news-button primary">
                <i class="fa-solid fa-plus"></i>
                Nouvelle actualité
            </a>
        </div>
    </div>
    <!-- =========================================================
         STATISTIQUES
    ========================================================= -->
    <div class="admin-news-stats">
        <div class="admin-news-stat">
            <div class="admin-news-stat-icon">
                <i class="fa-solid fa-newspaper"></i>
            </div>
            <div>
                <span class="admin-news-stat-value">
                    <?= count($news) ?>
                </span>
                <span class="admin-news-stat-label">
                    Actualités
                </span>
            </div>
        </div>
        <div class="admin-news-stat">
            <div class="admin-news-stat-icon published">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="admin-news-stat-value">
                    <?php
                    $published = 0;
                    foreach ($news as $article) {
                        if ((int) $article->status === 1) {
                            $published++;
                        }
                    }
                    ?>
                    <?= $published ?>
                </span>
                <span class="admin-news-stat-label">
                    Publiées
                </span>
            </div>
        </div>
        <div class="admin-news-stat">
            <div class="admin-news-stat-icon draft">
                <i class="fa-solid fa-file"></i>
            </div>
            <div>
                <span class="admin-news-stat-value">
                    <?= count($news) - $published ?>
                </span>
                <span class="admin-news-stat-label">
                    Brouillons
                </span>
            </div>
        </div>
    </div>
    <!-- =========================================================
         TABLE
         ========================================================= -->
    <div class="admin-news-card">
        <div class="admin-news-card-header">
            <div>
                <h2>
                    Liste des actualités
                </h2>
                <span>
                    <?= count($news) ?> actualité(s)
                </span>
            </div>
        </div>
        <?php if (empty($news)): ?>
            <div class="admin-news-empty">
                <div class="admin-news-empty-icon">
                    <i class="fa-solid fa-newspaper"></i>
                </div>
                <h3>
                    Aucune actualité
                </h3>
                <p>
                    Vous n'avez encore créé aucune actualité.
                </p>
                <a href="/admin/news/create"
                   class="admin-news-button primary">
                    <i class="fa-solid fa-plus"></i>
                    Créer une actualité
                </a>
            </div>
        <?php else: ?>
            <div class="admin-news-table-wrapper">
                <table class="admin-news-table">
                    <thead>
                    <tr>
                        <th>
                            Actualité
                        </th>
                        <th>
                            Catégorie
                        </th>
                        <th>
                            Statut
                        </th>
                        <th>
                            Statistiques
                        </th>
                        <th>
                            Date
                        </th>
                        <th class="text-right">
                            Actions
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($news as $article): ?>
                        <?php
                        $id        = (int) $article->id;
                        $category  = $categoryMap[(int) $article->id_cat] ?? null;
                        $title     = (string) $article->title;
                        $slug      = (string) $article->slug;
                        $status    = (int) $article->status;
                        $views     = (int) $article->views;
                        $likes     = (int) $article->likes;
                        $createdAt = (string) $article->created_at;
                        ?>
                        <tr>
                            <!-- =================================================
                                 TITRE
                            ================================================= -->
                            <td>
                                <div class="admin-news-title">
                                    <?php if (!empty($article->image)): ?>
                                        <div class="admin-news-thumbnail">
                                            <img src="<?= htmlspecialchars((string) $article->image,ENT_QUOTES,'UTF-8') ?>" alt="">
                                        </div>
                                    <?php else: ?>
                                        <div class="admin-news-thumbnail placeholder">
                                            <i class="fa-solid fa-newspaper"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="admin-news-title-content">
                                        <strong>
                                            <?= htmlspecialchars($title,ENT_QUOTES,'UTF-8'); ?>
                                        </strong>
                                        <span>
                                            <?= htmlspecialchars($slug,ENT_QUOTES,'UTF-8'); ?>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <!-- =================================================
                                 CATEGORIE
                            ================================================= -->
                            <td>
                                <?php if ($category !== null): ?>
                                    <span
                                        class="admin-news-category"
                                        style="--category-color: <?= htmlspecialchars((string) $category->color,ENT_QUOTES,'UTF-8'); ?>">
                                        <span class="category-dot"></span>
                                        <?= htmlspecialchars((string) $category->name,ENT_QUOTES,'UTF-8') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="admin-news-no-category">
                                        Sans catégorie
                                    </span>
                                <?php endif; ?>
                            </td>
                            <!-- =================================================
                                 STATUT
                            ================================================= -->
                            <td>
                                <?php if ($status === 1): ?>
                                    <span class="admin-news-status published">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Publiée
                                    </span>
                                <?php else: ?>
                                    <span class="admin-news-status draft">
                                        <i class="fa-solid fa-file"></i>
                                        Brouillon
                                    </span>
                                <?php endif; ?>
                            </td>
                            <!-- =================================================
                                 STATISTIQUES
                            ================================================= -->
                            <td>
                                <div class="admin-news-metrics">
                                    <span title="Vues">
                                        <i class="fa-regular fa-eye"></i>
                                        <?= number_format($views,0,',',' ') ?>
                                    </span>
                                    <span title="Likes">
                                        <i class="fa-regular fa-heart"></i>
                                        <?= number_format($likes,0,',',' ') ?>
                                    </span>
                                </div>
                            </td>
                            <!-- =================================================
                                 DATE
                                 ================================================= -->
                            <td>
                                <span class="admin-news-date">
                                    <i class="fa-regular fa-calendar"></i>
                                    <?= Common::TransformDate(htmlspecialchars(
                                        $createdAt,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ),'MEDIUM', 'MEDIUM') ?>
                                </span>
                            </td>
                            <!-- =================================================
                                 ACTIONS
                                 ================================================= -->
                            <td>
                                <div class="admin-news-row-actions">
                                    <a href="/admin/news/edit?id=<?= $id ?>" class="admin-news-icon-button" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="/news/view?slug=<?= urlencode($slug) ?>" target="_blank" class="admin-news-icon-button" title="Voir">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <form method="post" action="/admin/news/status">
                                        <?= $csrf ?>
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <input type="hidden" name="status" value="<?= $status === 1 ? 0 : 1 ?>">
                                        <button type="submit" class="admin-news-icon-button <?= $status === 1 ? 'warning' : 'success' ?>" title="<?= $status === 1 ? 'Dépublier' : 'Publier' ?>">
                                            <?php if ($status === 1): ?>
                                                <i class="fa-solid fa-eye-slash"></i>
                                            <?php else: ?>
                                                <i class="fa-solid fa-check"></i>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                    <!-- Supprimer -->
                                    <form method="post" action="/admin/news/delete" onsubmit="return confirm('Voulez-vous vraiment supprimer cette actualité ?');">
                                        <?= $csrf ?>
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <button type="submit" class="admin-news-icon-button danger" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>