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

$csrf = $csrf ?? '';

?>

<div class="groups-page">

    <div class="groups-header">

        <div>
            <h1>
                <i class="fa-solid fa-user-group"></i>
                Nouveau groupe
            </h1>

            <p>
                Créez un nouveau groupe pour les membres de Bel-CMS.
            </p>
        </div>

        <div class="groups-header-actions">

            <a href="/admin/groups" class="groups-btn groups-btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Retour aux groupes
            </a>

        </div>

    </div>


    <div class="group-form-card">

        <form method="post" action="/admin/groups/create">
            <?= $csrf ?>
            <div class="group-form-grid">
                <!-- Nom -->
                <div class="group-form-group group-form-full">

                    <label for="name">
                        Nom du groupe
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        maxlength="32"
                        required
                        autocomplete="off"
                        placeholder="Exemple : MODERATORS"
                    >

                    <small>
                        Maximum 32 caractères.
                    </small>

                </div>


                <!-- Couleur -->

                <div class="group-form-group">

                    <label for="color">
                        Couleur du groupe
                    </label>

                    <div class="group-color-wrapper">

                        <input
                            type="color"
                            id="color_picker"
                            value="#2381c0"
                        >

                        <input
                            type="text"
                            id="color"
                            name="color"
                            value="#2381c0"
                            maxlength="9"
                            pattern="^#[a-fA-F0-9]{6,8}$"
                            required
                        >

                    </div>

                    <small>
                        Format : #RRGGBB ou #RRGGBBAA.
                    </small>

                </div>


                <!-- Image -->

                <div class="group-form-group">

                    <label for="image">
                        Image
                    </label>

                    <input
                        type="text"
                        id="image"
                        name="image"
                        maxlength="500"
                        placeholder="/uploads/groups/moderators.png"
                    >

                    <small>
                        URL ou chemin de l'image du groupe.
                    </small>

                </div>


                <!-- Description -->

                <div class="group-form-group group-form-full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        placeholder="Décrivez le rôle de ce groupe..."
                    ></textarea>

                </div>

            </div>


            <div class="group-form-info">

                <i class="fa-solid fa-circle-info"></i>

                <div>
                    <strong>Identifiant automatique</strong>

                    <p>
                        Bel-CMS attribuera automatiquement le prochain
                        <code>id_group</code> disponible à ce groupe.
                    </p>
                </div>

            </div>


            <div class="group-form-actions">

                <a
                    href="/admin/groups"
                    class="groups-btn groups-btn-secondary"
                >
                    Annuler
                </a>

                <button
                    type="submit"
                    class="groups-btn groups-btn-primary"
                >
                    <i class="fa-solid fa-plus"></i>
                    Créer le groupe
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