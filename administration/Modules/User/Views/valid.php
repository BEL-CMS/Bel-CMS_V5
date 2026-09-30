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
        <div class="admin-users-table" style="margin-bottom: 20px;">
            <!-- En-tête -->
            <div class="admin-users-table-header">
                <div>
                    Utilisateur
                </div>
                <div>
                    Email
                </div>
                <div>
                    Statut
                </div>
                <div>
                    2FA
                </div>
                <div>
                    Actions
                </div>
            </div>
            <!-- =================================================
                 Membres en attente
                 ================================================= -->
            <?php foreach ($pendingUsers as $item): ?>
                <?php
                $pendingId = (int) (
                    $item->id ?? 0
                );
                $username = trim(
                    (string) ($item->username ?? '')
                );
                $email = trim(
                    (string) ($item->email ?? '')
                );
                ?>
                <div class="admin-user-row">
                    <!-- Utilisateur -->
                    <div class="admin-user-name">
                        <div class="admin-user-avatar">
                            <i class="fa-solid fa-user-clock"></i>
                        </div>
                        <div>
                            <strong>
                                <?= htmlspecialchars(
                                    $username,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </strong>
                        </div>
                    </div>
                    <!-- Email -->
                    <div class="admin-user-email">
                        <?= htmlspecialchars(
                            $email,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>
                    <!-- Statut -->
                    <div>
                        <span class="admin-user-status">
                            En attente
                        </span>
                    </div>
                    <!-- 2FA -->
                    <div>
                        <span class="admin-user-2fa disabled">
                            <i class="fa-solid fa-shield"></i>
                            Non activée
                        </span>
                    </div>
                    <!-- Actions -->
                    <div class="admin-user-actions">
                        <?php if ($pendingId > 0): ?>
                            <span class="admin-user-action" title="Membre en attente de validation">
                                <i class="fa-solid fa-clock"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>