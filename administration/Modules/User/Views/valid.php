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

use BelCMS\system\Common;

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
        <input type="search" name="search"
            value="<?= htmlspecialchars(
                $search,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            placeholder="Rechercher un utilisateur..."
            autocomplete="off"
        >
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
    <!-- =====================================================
         Membres en attente
         ===================================================== -->
    <?php if ($pendingUsers !== []): ?>
    <table class="BelCMS_TABLES" class="display">
		<thead>
			<tr>
				<th>Utilisateur</th>
				<th>Email</th>
				<th>Date d'inscription</th>
				<th>Statut</th>
				<th>2FA</th>
                <th>Actions</th>
			</tr>
		</thead> 
        <tbody>
            <!-- =================================================
                 Membres en attente
                 ================================================= -->
            <?php foreach ($pendingUsers as $item): ?>
                <?php
                $pendingId = (int) (
                    $item->id ?? 0
                );
                $hash_key = trim(
                    (string) ($item->hash_key ?? '')
                );
                $username = trim(
                    (string) ($item->username ?? '')
                );
                $email = trim(
                    (string) ($item->email ?? '')
                );
                ?>
                <tr>
                    <td>
                        <i class="fa-solid fa-user-clock"></i> 
                        <strong> <?= htmlspecialchars($username,ENT_QUOTES,'UTF-8'); ?></strong>                        
                    </td>
                    <!-- Email -->
                    <td class="admin-user-email">
                        <?= htmlspecialchars(
                            $email,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                    <td class="admin-user-datecreate">
                        <?= Common::TransformDate(htmlspecialchars($item->created_at,ENT_QUOTES,'UTF-8'),'MEDIUM', 'MEDIUM'); ?>
                    <!-- Statut -->
                    </td>
                    <td>
                        <span class="admin-user-status">
                            En attente
                        </span>
                    </td>
                    <!-- 2FA -->
                    <td>
                        <span class="admin-user-2fa disabled">
                            <i class="fa-solid fa-shield"></i>
                            Non activée
                        </span>
                    </td>
                    <!-- Actions -->
                    <td>
                        <?php if ($pendingId > 0): ?>
                            <form method="post" action="/admin/users/valid/validate" style="display:inline-block">
                                <?= $csrf ?>
                                <input type="hidden" name="hash_key" value="<?= htmlspecialchars($hash_key,ENT_QUOTES,'UTF-8') ?>">
                                <button type="submit" class="admin-user-button admin-user-color-green">
                                    <i class="fa-solid fa-check"></i>  Valider
                                </button>
                            </form>
                            <form method="post" action="/admin/users/valid/delete" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');" style="display:inline-block">
                                <?= $csrf ?>
                                <input type="hidden" name="hash_key" value="<?= htmlspecialchars($hash_key,ENT_QUOTES,'UTF-8') ?>">
                                <button type="submit" class="admin-user-button admin-user-color-red">
                                    <i class="fa-solid fa-trash"></i> Supprimer
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>