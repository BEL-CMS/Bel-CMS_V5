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
$csrf = $csrf ?? '';
$news = array();

?>
<div class="admin-news-card">
	<div class="admin-news-card-header">
		<div>
			<h2>
				Liste des logs
			</h2>
		</div>
	</div>
	<?php if (empty($logs)): ?>
		<div class="admin-news-empty">
			<div class="admin-news-empty-icon">
				<i class="fa-solid fa-newspaper"></i>
			</div>
			<h3>
				Aucune information
			</h3>
		</div>
	<?php else: ?>
		<div class="admin-news-table-wrapper">
			<table class="admin-news-table">
				<thead>
				<tr>
					<th>Utilisateurs</th>
					<th>IP</th>
					<th>Date</th>
					<th>Catégories</th>
					<th>Pages</th>
					<th>Messages</th>
					<th>Alert</th>
					<th class="text-right">Actions</th>
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
					if ($item->danger == 1) {
						$name_danger = 'Alert !';
						$bgcolorDanger = 'draft';
					} else if ($item->danger == 0) {
						$name_danger = 'Tout est conforme';
						$bgcolorDanger = 'published';
					} else {
						$name_danger = 'Banni';
						$bgcolorDanger = 'admin-user-color-red';
					}
					?>
					<tr>
						<td>
							<div class="admin-news-title">
								<div class="admin-news-title-content">
									<strong>
										<?= $user; ?>
									</strong>
								</div>
							</div>
						</td>
						<td><?= htmlspecialchars($item->ip,ENT_QUOTES,'UTF-8'); ?></td>
						<td>
							<span class="admin-news-date">
								<i class="fa-regular fa-calendar"></i><?= $date; ?>
							</span>
						</td>
						<td><span class="admin-news-category" style="--category-color: #2381c0">
							<span class="category-dot"></span><?= $type; ?></span>
						</td>
						<td><?= htmlspecialchars($item->message,ENT_QUOTES,'UTF-8'); ?></td>
						<td><?= htmlspecialchars($item->module,ENT_QUOTES,'UTF-8'); ?></td>
						<td><span class="admin-news-status <?= $bgcolorDanger; ?>"><?= $name_danger; ?></span></td>
						<td>
							<?php if($item->isban === false): ?>
							<form action="/admin/logs/ban" method="post" onsubmit="return confirm('Confirmez-vous votre intention de bannir cet utilisateur ?');">
								<?= $csrf ?>
								<input type="hidden" name="ip" value="<?= $item->ip; ?>">
								<input type="hidden" name="hashKey" value="<?= $item->user ?>">
								<button class="btn-action red belcms_tooltip_left" data="Bannir" type="submit"><i class="fa-solid fa-user-xmark"></i></button>
							</form>
							<?php endif; ?>
							<form action="/admin/logs/eye">
								<?= $csrf ?>
								<input type="hidden" value="<?= (int) $item->user; ?> ?>" name="hashkey">
								<button class="btn-action blue belcms_tooltip_left" data="Voir" type="submit"><i class="fa-solid fa-eye"></i></button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>
							