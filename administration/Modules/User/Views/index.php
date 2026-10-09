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

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;
$users = $users ?? [];
$pendingUsers = $pendingUsers ?? [];
$page = max(
    1,
    (int) ($page ?? 1)
);
$pages = max(
    1,
    (int) ($pages ?? 1)
);
$total = max(
    0,
    (int) ($total ?? 0)
);
$search = (string) ($search ?? '');
?>
<div class="admin-users">
    <div class="admin-users-header">
        <h1>
            <i class="fa-solid fa-users"></i>
            Utilisateurs
        </h1>
        <p>
            Gestion des comptes utilisateurs de Bel-CMS.
            <?= $total ?>
            utilisateur<?= $total > 1 ? 's' : '' ?>
            enregistré<?= $total > 1 ? 's' : '' ?>.
        </p>
    </div>

    <form method="get" action="/admin/users" class="admin-users-search">
        <input  type="search"  name="search"  value="<?= htmlspecialchars($search,ENT_QUOTES,'UTF-8'); ?>" placeholder="Rechercher un utilisateur..." autocomplete="on">
        <button type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
            Rechercher
        </button>
        <?php if ($search !== ''): ?>
            <a href="/admin/users" class="admin-user-action" title="Réinitialiser la recherche">
                <i class="fa-solid fa-xmark"></i>
            </a>
        <?php endif; ?>
    </form>

    <table class="BelCMS_TABLES">
		<thead>
			<tr>
				<th>Utilisateur</th>
				<th>Email</th>
                <th>IP</th>
				<th>Statut</th>
				<th>2FA</th>
				<th>Actions</th>
			</tr>
		</thead> 
        <tbody>
        <?php if ($users === []): ?>
            <!-- Aucun résultat -->
            <tr>
                <td colspan="6">
                    <div class="admin-users-empty">
                        <i class="fa-solid fa-user-slash"></i>
                        <div>Aucun utilisateur trouvé.</div>
                        <?php if ($search !== ''): ?>
                        <small>
                            Aucun résultat pour :
                            <strong>
                                <?= htmlspecialchars($search,ENT_QUOTES,'UTF-8'); ?>
                            </strong>
                        </small>
                <?php endif; ?>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($users as $item): ?>
                <?php
                $hashKey = trim(
                    (string) ($item->hash_key ?? '')
                );
                $username = trim(
                    (string) ($item->username ?? '')
                );
                $email = trim(
                    (string) ($item->email ?? '')
                );
                $valid = (int) (
                    $item->valid ?? 0
                );
                $twoFactor = (int) (
                    $item->two_factor_enabled ?? 0
                );
                ?>
                <tr>
                    <td>
                        <i class="fa-solid fa-user"></i><?= htmlspecialchars($username,ENT_QUOTES,'UTF-8'); ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($email,ENT_QUOTES,'UTF-8'); ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($item->ip,ENT_QUOTES,'UTF-8'); ?>
                    </td>
                    <td>
                        <?php if ($item->ban === 1): ?>
                            <span class="admin-user-status admin-user-color-red">
                                Banni
                            </span>
                        <?php else: ?>
                            <span class="admin-user-status admin-user-color-green">
                                Actif
                            </span>
                        <?php endif; ?>
                    </td>
                    <!-- 2FA -->
                     <td>
                        <?php if ($twoFactor === 1): ?>
                            <span class="admin-user-2fa enabled">
                                <i class="fa-solid fa-shield-halved"></i>
                                Activée
                            </span>
                        <?php else: ?>
                            <span class="admin-user-2fa disabled">
                                <i class="fa-solid fa-shield"></i>
                                Désactivée
                            </span>
                        <?php endif; ?>
                    </td>
                    <!-- Actions -->
                    <td>
                        <?php if ($hashKey !== ''): ?>
                            <!-- Modifier -->
                            <a href="/admin/users/edit?hash_key=<?= urlencode($hashKey) ?>" class="admin-user-action admin-user-color-green" title="Modifier l'utilisateur">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <!-- Sessions -->
                            <a href="/admin/users/sessions?hash_key=<?= urlencode($hashKey) ?>" class="admin-user-action admin-user-color-blue" title="Sessions">
                                <i class="fa-solid fa-laptop"></i>
                            </a>
                            <!-- Supprimer -->
                            <form method="post" action="/admin/users/delete" style="display: inline;" onsubmit="return confirm(
                                    'ATTENTION !\n\n' +
                                    'Vous êtes sur le point de supprimer définitivement cet utilisateur et ses données associées.\n\n' +
                                    'Cette action est irréversible.\n\n' +
                                    'Voulez-vous vraiment continuer ?'
                                );">
                                <?= $csrf ?>
                                <input type="hidden" name="hash_key" value="<?= htmlspecialchars($hashKey, ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="admin-user-action admin-user-color-red" title="Supprimer l'utilisateur">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <?php if ($pages > 1): ?>
        <div class="admin-users-pagination">
            <?php if ($page > 1): ?>
                <a href="/admin/users?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" title="Page précédente">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="active">
                        <?= $i ?>
                    </span>
                <?php else: ?>
                    <a href="/admin/users?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $pages): ?>
                <a href="/admin/users?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" title="Page suivante">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>