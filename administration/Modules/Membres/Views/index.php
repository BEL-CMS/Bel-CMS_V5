<?php
declare(strict_types=1);
$members = is_array($members ?? null) ? $members : [];
$page = max(1, (int) ($page ?? 1));
$pages = max(1, (int) ($pages ?? 1));
$total = max(0, (int) ($total ?? 0));
$search = (string) ($search ?? '');
$e = static fn(mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>
<section class="admin-members">
    <header class="admin-members__header">
        <div><span class="admin-members__eyebrow">BEL-CMS V5 · ADMINISTRATION</span><h1>Gestion des membres</h1><p>Rechercher et consulter les comptes enregistrés.</p></div>
        <div class="admin-members__total"><strong><?= $total ?></strong><span>comptes</span></div>
    </header>
    <form class="admin-members__search" action="/admin/members" method="get" role="search">
        <label class="visually-hidden" for="admin-members-search">Rechercher un membre</label>
        <input id="admin-members-search" name="search" type="search" maxlength="100" value="<?= $e($search) ?>" placeholder="Rechercher par pseudo…">
        <button type="submit">Rechercher</button>
        <?php if ($search !== ''): ?><a href="/admin/members">Effacer</a><?php endif; ?>
    </form>
    <div class="admin-members__table-wrap"><table class="admin-members__table">
        <thead><tr><th>Pseudo</th><th>État</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if ($members === []): ?><tr><td colspan="3" class="admin-members__empty">Aucun membre trouvé.</td></tr><?php endif; ?>
        <?php foreach ($members as $member): ?>
            <?php $hash = (string) ($member->hash_key ?? ''); $username = (string) ($member->username ?? 'Membre'); $valid = (int) ($member->valid ?? 0) === 1; ?>
            <tr>
                <td><strong><?= $e($username) ?></strong></td>
                <td><span class="admin-members__status <?= $valid ? 'is-valid' : 'is-pending' ?>"><?= $valid ? 'Actif' : 'En attente / inactif' ?></span></td>
                
                <td><?php if ($hash !== ''): ?><a class="admin-members__action" href="/admin/members/edit?hash_key=<?= rawurlencode($hash) ?>">Modifier le profil</a> · <a class="admin-members__action" href="/admin/users/edit?hash_key=<?= rawurlencode($hash) ?>">Gérer le compte <span aria-hidden="true">→</span></a><?php else: ?>—<?php endif; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php if ($pages > 1): ?><nav class="admin-members__pagination" aria-label="Pagination">
        <?php if ($page > 1): ?><a href="/admin/members?<?= http_build_query(['search' => $search, 'page' => $page - 1]) ?>">← Précédent</a><?php endif; ?>
        <span>Page <?= $page ?> / <?= $pages ?></span>
        <?php if ($page < $pages): ?><a href="/admin/members?<?= http_build_query(['search' => $search, 'page' => $page + 1]) ?>">Suivant →</a><?php endif; ?>
    </nav><?php endif; ?>
</section>
