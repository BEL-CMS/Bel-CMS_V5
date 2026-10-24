<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */

declare(strict_types=1);

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

/**
 * Bel-CMS V5
 * Administration - Édition d'un groupe
 *
 * Variables disponibles :
 * $group
 * $csrf
 * $isSystem
 */
?>
<div class="groups-page">
    <div class="groups-header">
        <div>
            <h1>
                <i class="fa-solid fa-pen-to-square"></i>
                Modifier le groupe
            </h1>
            <p>
                Modifiez les informations du groupe
                <strong><?= htmlspecialchars($group->name, ENT_QUOTES, 'UTF-8') ?></strong>.
            </p>
        </div>
        <a href="/admin/groups" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Retour aux groupes
        </a>
    </div>
    <div class="group-form-container">
        <form method="post"
              action="/admin/groups/edit?id_group=<?= (int) $group->id_group ?>"
              class="group-form">
            <?= $csrf ?>
            <!-- Identifiant du groupe -->
            <div class="form-info">
                <div class="form-info-icon">
                    <i class="fa-solid fa-hashtag"></i>
                </div>
                <div>
                    <strong>
                        Identifiant du groupe
                    </strong>
                    <span>
                        <?= (int) $group->id_group ?>
                    </span>
                </div>
            </div>
            <!-- Nom -->
            <div class="form-group">
                <label for="name">
                    Nom du groupe
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="32"
                    required
                    value="<?= htmlspecialchars(
                        (string) $group->name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >
                <small>
                    Maximum 32 caractères.
                </small>
            </div>
            <!-- Couleur -->
            <div class="form-group">
                <label for="color">
                    Couleur du groupe
                </label>
                <div class="color-input">
                    <input
                        type="color"
                        id="color_picker"
                        value="<?= htmlspecialchars(
                            (string) $group->color,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                    <input
                        type="text"
                        id="color"
                        name="color"
                        maxlength="9"
                        value="<?= htmlspecialchars(
                            (string) $group->color,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >
                </div>
                <small>
                    Format accepté : #RRGGBB ou #RRGGBBAA.
                </small>
            </div>
            <!-- Image -->
            <div class="form-group">
                <label for="image">
                    Image
                </label>
                <input
                    type="text"
                    id="image"
                    name="image"
                    value="<?= htmlspecialchars(
                        (string) ($group->image ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    placeholder="URL ou chemin de l'image"
                >
                <small>
                    URL ou chemin vers l'image du groupe.
                </small>
            </div>
            <!-- Description -->
            <div class="form-group">
                <label for="description">
                    Description
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="5"
                ><?= htmlspecialchars(
                    (string) ($group->description ?? ''),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>
            </div>
            <?php if ($isSystem): ?>
                <div class="form-warning">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div>
                        <strong>
                            Groupe système
                        </strong>
                        <p>
                            Ce groupe est protégé par Bel-CMS.
                            Son identifiant ne peut pas être modifié
                            et le groupe ne peut pas être supprimé.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Actions -->
            <div class="form-actions">
                <a
                    href="/admin/groups"
                    class="btn-secondary"
                >
                    <i class="fa-solid fa-xmark"></i>
                    Annuler
                </a>
                <button
                    type="submit"
                    class="btn-primary"
                >
                    <i class="fa-solid fa-floppy-disk"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const picker = document.getElementById('color_picker');
    const color = document.getElementById('color');
    if (!picker || !color) {
        return;
    }
    picker.addEventListener('input', function () {
        color.value = picker.value;
    });
    color.addEventListener('input', function () {
        const value = color.value.trim();
        if (/^#[a-fA-F0-9]{6}$/.test(value)) {
            picker.value = value;
        }
    });
});
</script>