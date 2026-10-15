<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/

declare(strict_types=1);

use BelCMS\Core\User;
use BelCMS\system\Common;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

//debug($logs);
?>
    <table class="BelCMS_TABLES">
		<thead>
			<tr>
				<th>Utilisateurs</th>
				<th>IP</th>
                <th>Date</th>
				<th>Catégories</th>
				<th>Pages</th>
				<th>Messages</th>
				<th>Danger</th>
				<th>Action</th>
			</tr>
		</thead> 
        <tbody>
			<?php
			foreach ($logs as $item):
				if (User::isUserExist($item->user)) {
					$user = User::isUserToHahskey($item->user);
				} else {
					$user = 'Utilisateur supprimer';
				}
				$date = Common::TransformDate($item->date_activite, 'MEDIUM', 'MEDIUM');
				$type = ($item->type == 0) ? 'Module' : 'Administration';
				$style_danger = ($item->danger == 1) ? 'style="background: var(--admin-color-red); color:var(--bel-cms-color-light);"' : 'style="background: var(--bel-cms-color-green); color:var(--bel-cms-color-light);"';
				$name_danger = ($item->danger == 1) ? 'Alerte !' : 'Tout est conforme';

			?>
			<tr>
				<td <?= $style_danger; ?>><?= $user; ?></td>
				<td <?= $style_danger; ?>><?= htmlspecialchars($item->ip,ENT_QUOTES,'UTF-8'); ?></td>
				<td <?= $style_danger; ?>><?= $date; ?></td>
				<td <?= $style_danger; ?>><?= $type; ?></td>
				<td <?= $style_danger; ?>><?= htmlspecialchars($item->module,ENT_QUOTES,'UTF-8'); ?></td>
				<td <?= $style_danger; ?>><?= htmlspecialchars($item->message,ENT_QUOTES,'UTF-8'); ?></td>
				<td <?= $style_danger; ?>><?= $name_danger; ?></td>
				<td>
					<a href="#" class="btn-action red belcms_tooltip_left" data="Bannir"><i class="fa-solid fa-user-xmark"></i></a>   
					<a href="#" class="btn-action blue belcms_tooltip_left" data="Voir"><i class="fa-solid fa-eye"></i></a>
			</tr>
			<?php 
			endforeach;
			?>
		</tbody>
	</table>