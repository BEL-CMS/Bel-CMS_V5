<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */
declare(strict_types=1);

/** @var object|null $news */
/** @var array $categories */
/** @var string $csrf */
/** @var array $errors */
$news = $news ?? null;
$categories = $categories ?? [];
$csrf = $csrf ?? '';
$errors = $errors ?? [];
if ($news === null) {
    return;
}
$id      = (int) $news->id;
$title   = (string) ($news->title ?? '');
$slug    = (string) ($news->slug ?? '');
$idCat   = (int) ($news->id_cat ?? 0);
$image   = (string) ($news->image ?? '');
$content = (string) ($news->content ?? '');
$tags    = (string) ($news->tags ?? '');
$status  = (int) ($news->status ?? 0);
?>
<div class="admin-news-form">
    <!-- =====================================================
         HEADER
         ===================================================== -->
    <div class="admin-news-form-header">
        <div>
            <h1>
                <i class="fa-solid fa-pen-to-square"></i>
                Modifier l'actualité
            </h1>
            <p>
                Modification de :
                <strong>
                    <?= htmlspecialchars(
                        $title,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
            </p>
        </div>
        <a
            href="/admin/news"
            class="admin-news-button secondary"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Retour aux actualités
        </a>
    </div>
    <!-- =====================================================
         ERRORS
         ===================================================== -->
    <?php if (!empty($errors)): ?>
        <div class="admin-news-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>
                    Impossible de modifier l'actualité.
                </strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li>
                            <?= htmlspecialchars(
                                (string) $error,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    <!-- =====================================================
         FORM
         ===================================================== -->
    <form
        method="post"
        action="/admin/news/edit?id=<?= $id ?>"
        class="admin-news-form-layout"
    >
        <?= $csrf ?>
        <input
            type="hidden"
            name="id"
            value="<?= $id ?>"
        >
        <!-- =================================================
             CONTENU PRINCIPAL
             ================================================= -->
        <div class="admin-news-form-main">
            <div class="admin-news-form-card">
                <div class="admin-news-form-card-header">
                    <h2>
                        <i class="fa-solid fa-pen"></i>
                        Contenu
                    </h2>
                </div>
                <!-- TITRE -->
                <div class="admin-news-field">
                    <label for="title">
                        Titre
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="<?= htmlspecialchars(
                            $title,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        maxlength="255"
                        required
                    >
                </div>
                <!-- SLUG -->
                <div class="admin-news-field">
                    <label for="slug">
                        Slug
                        <span>
                            URL de l'actualité
                        </span>
                    </label>
                    <div class="admin-news-input-prefix">
                        <span>
                            /news/view?slug=
                        </span>
                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            value="<?= htmlspecialchars(
                                $slug,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            maxlength="255"
                            required
                        >
                    </div>
                </div>
                <!-- CONTENU -->
                <div class="admin-news-field">
                    <label for="content">
                        Contenu
                    </label>
                    <textarea
                        id="content"
                        name="content"
                        rows="18"
                        required
                    ><?= htmlspecialchars(
                        $content,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>
                    <small>
                        Contenu de l'actualité.
                    </small>
                </div>
                <!-- TAGS -->
                <div class="admin-news-field">
                    <label for="tags">
                        Tags
                        <span>
                            Séparés par des virgules
                        </span>
                    </label>
                    <input
                        type="text"
                        id="tags"
                        name="tags"
                        value="<?= htmlspecialchars(
                            $tags,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="Bel-CMS, PHP, développement"
                    >
                </div>
            </div>
        </div>
        <!-- =================================================
             SIDEBAR
             ================================================= -->
        <div class="admin-news-form-sidebar">
            <!-- =================================================
                 CATEGORIE
                 ================================================= -->
            <div class="admin-news-form-card">
                <div class="admin-news-form-card-header">
                    <h2>
                        <i class="fa-solid fa-folder"></i>
                        Catégorie
                    </h2>
                </div>
                <div class="admin-news-field">
                    <label for="id_cat">
                        Catégorie
                    </label>
                    <select
                        id="id_cat"
                        name="id_cat"
                    >
                        <option value="0">
                            Sans catégorie
                        </option>
                        <?php foreach ($categories as $category): ?>
                            <?php
                            $categoryId = (int) $category->id_cat;
                            ?>
                            <option
                                value="<?= $categoryId ?>"
                                <?= $idCat === $categoryId
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    (string) $category->name,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- =================================================
                 IMAGE
                 ================================================= -->
            <div class="admin-news-form-card">
                <div class="admin-news-form-card-header">
                    <h2>
                        <i class="fa-solid fa-image"></i>
                        Image
                    </h2>
                </div>
                <div class="admin-news-field">
                    <label for="image">
                        URL de l'image
                    </label>
                    <input
                        type="text"
                        id="image"
                        name="image"
                        value="<?= htmlspecialchars(
                            $image,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="/uploads/news/image.jpg"
                    >
                    <?php if ($image !== ''): ?>
                        <div
                            class="admin-news-image-preview"
                            style="margin-top: 12px;"
                        >
                            <img
                                src="<?= htmlspecialchars(
                                    $image,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt=""
                            >
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- =================================================
                 PUBLICATION
                 ================================================= -->
            <div class="admin-news-form-card">
                <div class="admin-news-form-card-header">
                    <h2>
                        <i class="fa-solid fa-toggle-on"></i>
                        Publication
                    </h2>
                </div>
                <label class="admin-news-switch">
                    <input
                        type="checkbox"
                        name="status"
                        value="1"
                        <?= $status === 1
                            ? 'checked'
                            : '' ?>
                    >
                    <span class="admin-news-switch-slider"></span>
                    <span class="admin-news-switch-label">
                        Actualité publiée
                    </span>
                </label>
                <p class="admin-news-help">
                    Désactivez cette option pour transformer
                    l'actualité en brouillon.
                </p>
            </div>
            <!-- =================================================
                 INFORMATIONS
                 ================================================= -->
            <div class="admin-news-form-card">
                <div class="admin-news-form-card-header">
                    <h2>
                        <i class="fa-solid fa-circle-info"></i>
                        Informations
                    </h2>
                </div>
                <div class="admin-news-edit-info">
                    <div>
                        <span>ID</span>
                        <strong>
                            #<?= $id ?>
                        </strong>
                    </div>
                    <div>
                        <span>Vues</span>
                        <strong>
                            <?= number_format(
                                (int) ($news->views ?? 0),
                                0,
                                ',',
                                ' '
                            ) ?>
                        </strong>
                    </div>
                    <div>
                        <span>Likes</span>
                        <strong>
                            <?= number_format(
                                (int) ($news->likes ?? 0),
                                0,
                                ',',
                                ' '
                            ) ?>
                        </strong>
                    </div>
                    <div>
                        <span>Créée le</span>
                        <strong>
                            <?= htmlspecialchars(
                                (string) (
                                    $news->created_at ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </div>
                    <?php if (!empty($news->updated_at)): ?>
                        <div>
                            <span>Modifiée le</span>
                            <strong>
                                <?= htmlspecialchars(
                                    (string) $news->updated_at,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- =================================================
                 ACTIONS
                 ================================================= -->
            <div class="admin-news-form-actions">
                <button
                    type="submit"
                    class="admin-news-button primary"
                >
                    <i class="fa-solid fa-floppy-disk"></i>
                    Enregistrer les modifications
                </button>
                <a
                    href="/admin/news"
                    class="admin-news-button secondary"
                >
                    <i class="fa-solid fa-xmark"></i>
                    Annuler
                </a>
            </div>
        </div>
    </form>
</div>