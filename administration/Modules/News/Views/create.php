<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */
declare(strict_types=1);

/** @var array $categories */
/** @var string $csrf */
/** @var array $errors */
$categories = $categories ?? [];
$csrf = $csrf ?? '';
$errors = $errors ?? [];
$title = (string) ($old['title'] ?? '');
$slug = (string) ($old['slug'] ?? '');
$idCat = (int) ($old['id_cat'] ?? 0);
$image = (string) ($old['image'] ?? '');
$content = (string) ($old['content'] ?? '');
$tags = (string) ($old['tags'] ?? '');
$status = (int) ($old['status'] ?? 0);
?>
<div class="admin-news-form">
    <div class="admin-news-form-header">
        <div>
            <h1>
                <i class="fa-solid fa-newspaper"></i>
                Nouvelle actualité
            </h1>
            <p>
                Créez une nouvelle actualité pour votre site.
            </p>
        </div>
        <a href="/admin/news"
           class="admin-news-button secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Retour aux actualités
        </a>
    </div>
    <?php if (!empty($errors)): ?>
        <div class="admin-news-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>
                    Impossible de créer l'actualité.
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
    <form
        method="post"
        action="/admin/news/create"
        class="admin-news-form-layout"
    >
<?= $csrf ?>
        <!-- =====================================================
             CONTENU PRINCIPAL
             ===================================================== -->
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
                        placeholder="Titre de votre actualité"
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
                            placeholder="mon-actualite"
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
                        class="bel_cms_textarea_full"
                        placeholder="Écrivez le contenu de votre actualité..."
                    ><?= htmlspecialchars(
                        $content,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>
                    <small>
                        Le contenu pourra ensuite être enrichi avec
                        votre éditeur de texte.
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
        <!-- =====================================================
             SIDEBAR
             ===================================================== -->
        <div class="admin-news-form-sidebar">
            <!-- CATEGORIE -->
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
                            <option
                                value="<?= (int) $category->id_cat ?>"
                                <?= $idCat === (int) $category->id_cat
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
            <!-- IMAGE -->
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
                    <small>
                        URL ou chemin de l'image à afficher.
                    </small>
                </div>
            </div>
            <!-- STATUT -->
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
                        <?= $status === 1 ? 'checked' : '' ?>
                    >
                    <span class="admin-news-switch-slider"></span>
                    <span class="admin-news-switch-label">
                        Publier immédiatement
                    </span>
                </label>
                <p class="admin-news-help">
                    Si cette option est désactivée,
                    l'actualité sera enregistrée comme brouillon.
                </p>
            </div>
            <!-- ACTIONS -->
            <div class="admin-news-form-actions">
                <button
                    type="submit"
                    class="admin-news-button primary"
                >
                    <i class="fa-solid fa-floppy-disk"></i>
                    Enregistrer
                </button>
                <a
                    href="/admin/news"
                    class="admin-news-button secondary"
                >
                    Annuler
                </a>
            </div>
        </div>
    </form>
</div>