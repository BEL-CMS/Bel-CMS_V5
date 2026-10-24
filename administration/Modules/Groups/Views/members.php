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
 * Gestion des membres d'un groupe
 *
 * @var object $group
 * @var array  $members
 * @var string $csrf
 */
?>
<div class="groups-page">
    <!-- En-tête -->
    <div class="groups-header">
        <div>
            <a href="/admin/groups" class="group-back-link">
                <i class="fa-solid fa-arrow-left"></i>
                Retour aux groupes
            </a>
            <div class="groups-title-row">
                <div
                    class="group-title-color"
                    style="background-color: <?= htmlspecialchars(
                        (string) ($group->color ?? '#2381c0'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>;"
                ></div>
                <div>
                    <h1>
                        <?= htmlspecialchars(
                            (string) ($group->name ?? ''),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>
                    <p>
                        Gestion des membres du groupe
                    </p>
                </div>
            </div>
        </div>
        <div class="groups-header-actions">
            <a
                href="/admin/groups/edit?id_group=<?= (int) $group->id_group ?>"
                class="btn btn-secondary"
            >
                <i class="fa-solid fa-pen"></i>
                Modifier le groupe
            </a>
            <a
                href="/admin/groups/members/add?id_group=<?= (int) $group->id_group ?>"
                class="btn btn-primary"
            >
                <i class="fa-solid fa-user-plus"></i>
                Ajouter un membre
            </a>
        </div>
    </div>
    <!-- Informations du groupe -->
    <div class="group-members-info">
        <div class="group-members-info-item">
            <span class="group-members-info-icon">
                <i class="fa-solid fa-users"></i>
            </span>
            <div>
                <strong><?= count($members) ?></strong>
                <span>Membre<?= count($members) > 1 ? 's' : '' ?></span>
            </div>
        </div>
        <div class="group-members-info-item">
            <span
                class="group-members-color"
                style="background-color: <?= htmlspecialchars(
                    (string) ($group->color ?? '#2381c0'),
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>"
            ></span>
            <div>
                <strong>#<?= (int) $group->id_group ?></strong>
                <span>ID du groupe</span>
            </div>
        </div>
    </div>
    <!-- Liste des membres -->
    <div class="group-members-container">
        <div class="group-members-header">
            <div>
                <h2>
                    <i class="fa-solid fa-users"></i>
                    Membres
                </h2>
                <p>
                    Liste des utilisateurs appartenant à ce groupe.
                </p>
            </div>
            <div class="group-members-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="search"
                    id="groupMemberSearch"
                    placeholder="Rechercher un membre..."
                    autocomplete="off"
                >
            </div>
        </div>
        <?php if (empty($members)): ?>
            <div class="groups-empty">
                <div class="groups-empty-icon">
                    <i class="fa-solid fa-users-slash"></i>
                </div>
                <h3>Aucun membre</h3>
                <p>
                    Aucun utilisateur n'est actuellement présent
                    dans ce groupe.
                </p>
                <a
                    href="/admin/groups/members?id_group=<?= (int) $group->id_group ?>&action=add"
                    class="btn btn-primary"
                >
                    <i class="fa-solid fa-user-plus"></i>
                    Ajouter un membre
                </a>
            </div>
        <?php else: ?>
            <div class="group-members-list" id="groupMembersList">
                <?php foreach ($members as $member): ?>
                    <?php
                    $username = (string) ($member->username ?? '');
                    $email    = (string) ($member->email ?? '');
                    $valid    = (int) ($member->valid ?? 0);
                    $initial = strtoupper(
                        function_exists('mb_substr')
                            ? mb_substr($username, 0, 1)
                            : substr($username, 0, 1)
                    );
                    ?>
                    <div
                        class="group-member-card"
                        data-search="<?= htmlspecialchars(
                            strtolower($username . ' ' . $email),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >
                        <div class="group-member-avatar">
                            <?= htmlspecialchars(
                                $initial,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>
                        <div class="group-member-main">
                            <div class="group-member-name">
                                <strong>
                                    <?= htmlspecialchars(
                                        $username,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>
                                <?php if ($valid): ?>
                                    <span class="member-status active">
                                        <i class="fa-solid fa-circle"></i>
                                        Actif
                                    </span>
                                <?php else: ?>
                                    <span class="member-status inactive">
                                        <i class="fa-solid fa-circle"></i>
                                        Inactif
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="group-member-email">
                                <i class="fa-solid fa-envelope"></i>
                                <?= htmlspecialchars(
                                    $email,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>
                        </div>
                        <div class="group-member-actions">
                            <a
                                href="/admin/users/edit?hash_key=<?= urlencode(
                                    (string) ($member->hash_key ?? '')
                                ) ?>"
                                class="btn btn-secondary btn-small"
                            >
                                <i class="fa-solid fa-user"></i>
                                Voir
                            </a>
                            <form
                                method="post"
                                action="/admin/groups/members/remove"
                                onsubmit="return confirm('Voulez-vous vraiment retirer cet utilisateur du groupe ?');"
                            >
                                <?= $csrf ?>
                                <input
                                    type="hidden"
                                    name="id_group"
                                    value="<?= (int) $group->id_group ?>"
                                >
                                <input
                                    type="hidden"
                                    name="hash_key"
                                    value="<?= htmlspecialchars(
                                        (string) ($member->hash_key ?? ''),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >
                                <button
                                    type="submit"
                                    class="btn btn-danger btn-small"
                                >
                                    <i class="fa-solid fa-user-minus"></i>
                                    Retirer
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Aucun résultat de recherche -->
            <div
                class="groups-empty groups-search-empty"
                id="groupMembersSearchEmpty"
                style="display:none;"
            >
                <div class="groups-empty-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3>Aucun résultat</h3>
                <p>
                    Aucun membre ne correspond à votre recherche.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('groupMemberSearch');
    const list = document.getElementById('groupMembersList');
    const empty = document.getElementById('groupMembersSearchEmpty');
    if (!search || !list) {
        return;
    }
    const members = list.querySelectorAll('.group-member-card');
    search.addEventListener('input', function () {
        const value = this.value.trim().toLowerCase();
        let visible = 0;
        members.forEach(function (member) {
            const text = member.dataset.search || '';
            if (value === '' || text.includes(value)) {
                member.style.display = '';
                visible++;
            } else {
                member.style.display = 'none';
            }
        });
        if (empty) {
            empty.style.display = visible === 0 ? '' : 'none';
        }
    });
});
</script>