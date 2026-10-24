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

use BelCMS\Core\Notification;

/** @var array $config */
/** @var string $csrf */
$config = $config ?? [];
$csrf   = $csrf ?? '';

if (!empty($errors)):
    Notification::error($errors, 'Erreur');
endif;

if (!empty($save)){
    Notification::success($save, 'Paramètre');
}
?>
<div class="admin-panel-two-columns">
        <div class="admin-panel-card">

            <div class="admin-panel-card-header">
                <h2>
                    <i class="fa-solid fa-circle-info"></i>
                    Informations techniques
                </h2>
            </div>

            <form class="admin-panel-card-body" action="/admin/settings/save" method="post">
                <table>
                    <tr>
                        <td style="width: 25%;">Titre du site :</td>
                        <td style="width: 75%;"><input type="text" id="title" name="title" value="<?= htmlspecialchars((string) $config['NAME_WEB'],ENT_QUOTES,'UTF-8') ?>" maxlength="100" required></td>
                    </tr>
                    <tr>
                        <td style="width: 25%;">Description du site :</td>
                        <td style="width: 75%;"><input type="text" id="description" name="description" value="<?= htmlspecialchars((string) $config['DESCRIPTION'],ENT_QUOTES,'UTF-8') ?>" maxlength="100"></td>
                    </tr>
                    <tr>
                        <td style="width: 25%;">Mot-clés</td>
                        <td style="width: 75%;"><input type="text" id="key" name="key" value="<?= htmlspecialchars((string) $config['KEYWORD'],ENT_QUOTES,'UTF-8') ?>" maxlength="100"></td>
                    </tr>
                    <tr>
                        <td style="width: 25%;">Charte d'enregistrement</td>
                        <td style="width: 75%;">
                            <textarea rows="4" cols="15" name="charte"><?= htmlspecialchars((string) $config['CHATER'],ENT_QUOTES,'UTF-8') ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%;">Email du site</td>
                        <td style="width: 75%;"><input type="text" name="mail" class="form-control" placeholder="no_reply@bel-cms.dev" value="<?= htmlspecialchars((string) $config['EMAIL'],ENT_QUOTES,'UTF-8') ?>" required></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?= $csrf ?>
                            <button type="submit" class="btn-primary">
                                 <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
                            </button>
                        </td>
                </table>
            </form>
        </div>
</div>