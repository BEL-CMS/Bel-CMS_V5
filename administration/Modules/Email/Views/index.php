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

use BelCMS\Core\Notification;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;
$csrf = $csrf ?? '';

if (isset($return)) {
	if ($return === true) {
		Notification::success('Opération enregistrée avec succès.', 'E-mail');
	} else {
		Notification::error('Erreur lors de la mise à jour', 'E-mail');
	}
}
?>
<div id="belcms_admin_grid">
	<div class="belcms_admin_content">
		<div class="belcms_admin_content_grid_2">
			<div class="belcms_admin_grid_title">
				Paramètres e-mail
			</div>
			<form class="belcms_form" action="/admin/email/send" method="post">
				<?= $csrf ?>
				<div class="belcms_form_prefix">
					<label>host</label>
					<input type="text" value="<?= $config['host']; ?>" name="host" required>
				</div>
				<div class="belcms_form_prefix">
					<label>Port</label>
					<select name="port">
						<option value="<?= $config['port']; ?>"><?= $config['port']; ?></option>
						<option value="587">587</option>
						<option value="465">465</option>
						<option value="2525">2525</option>
					</select>
				</div>
				<div class="belcms_form_prefix">
					<label>SMTPAuth</label>
					<select name="SMTPAuth">
						<?php
						if ($config['SMTPAuth'] == 'true') {
							$SMTPAuth = true;
							$SMTPAuthtitle = 'Activer';
						} else {
							$SMTPAuth = false;
							$SMTPAuthtitle = 'Désactiver';
						}
						?>
						<option selected value="<?= $SMTPAuth; ?>"><?= $SMTPAuthtitle; ?></option>
						<option value="false">Désactiver</option>
						<option value="true">Activer</option>
					</select>
				</div>
				<div class="belcms_form_prefix">
					<label>SMTPAutoTLS</label>
					<select name="SMTPAutoTLS">
						<?php
						if ($config['SMTPAutoTLS'] == 'true') {
							$SMTPAutoTLS = true;
							$SMTPAutoTLSTitle = 'Activer';
						} else {
							$SMTPAutoTLS = false;
							$SMTPAutoTLSTitle = 'Désactiver';
						}
						?>
						<option selected value="<?= $SMTPAutoTLS; ?>"><?= $SMTPAutoTLSTitle; ?></option>
						<option value="false">Désactiver</option>
						<option value="true">Activer</option>
					</select>
				</div>
				<div class="belcms_form_prefix">
					<label>WordWrap</label>
					<input type="number" min="65" name="WordWrap" value="<?= $config['WordWrap']; ?>">
				</div>
				<div class="belcms_form_prefix">
					<label>E-mail en HTML</label>
					<select name="html">
						<?php
						if ($config['HTML'] == 'true') {
							$selectedHtmlTrue = "selected";
							$selectedHtmlFalse = "";
						} else {
							$selectedHtmlTrue = "";
							$selectedHtmlFalse = "selected";
						}
						?>
						<option <?= $selectedHtmlTrue.$selectedHtmlFalse ?> value="false">Désactiver</option>
						<option <?= $selectedHtmlTrue.$selectedHtmlFalse ?> value="true">Activer</option>
					</select>
				</div>	
				<div class="belcms_form_prefix">
					<label>L'expediteur de l'e-mail</label>
					<input type="email" name="sender_email" value="<?= $config['sender_email']; ?>">
				</div>
				<div class="belcms_form_prefix">
					<label>Nom de l'expediteur de l'e-mail</label>
					<input type="text" name="sender_name" value="<?= $config['sender_name']; ?>">
				</div>
				<div class="belcms_form_prefix">
					<label>Charset</label>
					<select name="charset">
						<option value="UTF-8">UTF-8</option>
						<option value="Windows-1252">Windows-1252</option>
						<option value="ISO-8859-1">ISO-8859-1</option>
					</select>
				</div>	
				<div class="belcms_form_prefix">
					<label>Nom d'utilisateur</label>
					<input type="email" value="<?= $config['username'] ?>" name="username" required>
				</div>
				<div class="belcms_form_prefix">
					<label>Mot de passe</label>
					<input type="password" value="<?= $config['password'] ?>" name="password" required>
				</div>
				<button type="submit" class="btn-action blue"><i class="fa-solid fa-floppy-disk"></i>  Enregistrer les modifications</button>
			</form>
		</div>
	</div>
</div>