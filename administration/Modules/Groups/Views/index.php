<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */
declare(strict_types=1);
/**
 * @var array $groups
 * @var string $csrf
 */
?>
<div class="groups-page">
    <!-- En-tête -->
    <div class="groups-header">
        <div class="groups-header-content">
            <div>
                <span class="groups-eyebrow">
                    <i class="fa-solid fa-users-gear"></i>
                    Administration
                </span>
                <h1>
                    Gestion des groupes
                </h1>
                <p>
                    Gérez les groupes utilisateurs, leurs permissions
                    et leurs informations.
                </p>
            </div>
            <div class="groups-header-actions">
                <a href="/admin/groups/create" class="btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Nouveau groupe
                </a>
            </div>
        </div>
    </div>
    <!-- Contenu -->
    <div class="groups-content">
        <?php if (empty($groups)): ?>
            <div class="groups-empty">
                <div class="groups-empty-icon">
                    <i class="fa-solid fa-users-slash"></i>
                </div>
                <h2>Aucun groupe</h2>
                <p>
                    Aucun groupe utilisateur n'est actuellement disponible.
                </p>
                <a href="/admin/groups/create" class="btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Créer un groupe
                </a>
            </div>
        <?php else: ?>
            <div class="groups-grid">
                <?php foreach ($groups as $group): ?>
                    <?php
                    $idGroup = (int) $group->id_group;
                    $name = (string) $group->name;
                    $description = trim((string) ($group->description ?? ''));
                    $color = trim((string) ($group->color ?? '#000000'));
                    if ($color === '') {
                        $color = '#000000';
                    }
                    $memberCount = (int) ($group->member_count ?? 0);
                    $isSystem = in_array(
                        $idGroup,
                        [1, 2],
                        true
                    );
                    /*
                     * Sécurisation de la couleur utilisée
                     * directement dans le style.
                     */
                    if (!preg_match('/^#[a-fA-F0-9]{6,8}$/', $color)) {
                        $color = '#000000';
                    }
                    ?>
                    <article
                        class="group-card <?= $isSystem ? 'group-card-system' : '' ?>"
                        style="--group-color: <?= htmlspecialchars($color, ENT_QUOTES, 'UTF-8') ?>;"
                    >
                        <!-- Barre de couleur -->
                        <div class="group-card-color"></div>
                        <!-- En-tête de la carte -->
                        <div class="group-card-header">
                            <div class="group-card-icon">
                                <?php if (!empty($group->image)): ?>
                                    <img
                                        src="<?= htmlspecialchars(
                                            (string) $group->image,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        alt=""
                                    >
                                <?php else: ?>
                                    <i class="fa-solid fa-users"></i>
                                <?php endif; ?>
                            </div>
                            <div class="group-card-title">
                                <h2>
                                    <?= htmlspecialchars(
                                        $name,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h2>
                                <span class="group-id">
                                    ID groupe :
                                    <?= $idGroup ?>
                                </span>
                            </div>
                            <?php if ($isSystem): ?>
                                <span class="group-system-badge">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    Système
                                </span>
                            <?php endif; ?>
                        </div>
                        <!-- Description -->
                        <div class="group-card-body">
                            <div class="group-description">
                                <?php if ($description !== ''): ?>
                                    <?= nl2br(
                                        htmlspecialchars(
                                            $description,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )
                                    ) ?>
                                <?php else: ?>
                                    <span class="group-no-description">
                                        Aucune description.
                                    </span>
                                <?php endif; ?>
                            </div>
                            <!-- Membres -->
                            <div class="group-members">
                                <div class="group-members-icon">
                                    <i class="fa-solid fa-user-group"></i>
                                </div>
                                <div>
                                    <strong>
                                        <?= $memberCount ?>
                                    </strong>
                                    <span>
                                        <?= $memberCount === 1
                                            ? 'membre'
                                            : 'membres'
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Actions -->
                    <div class="group-card-footer">
                        <div class="group-card-actions">
                            <!-- Membres -->
                            <a
                                href="/admin/groups/members?id_group=<?= $idGroup ?>"
                                class="btn-primary"
                                title="Gérer les membres du groupe"
                            >
                                <i class="fa-solid fa-users"></i>
                                Membres
                            </a>
                            <!-- Modifier -->
                            <a
                                href="/admin/groups/edit?id_group=<?= $idGroup ?>"
                                class="btn-secondary"
                                title="Modifier le groupe"
                            >
                                <i class="fa-solid fa-pen"></i>
                                Modifier
                            </a>
                            <!-- Suppression -->
                            <?php if ($isSystem): ?>
                                <button
                                    type="button"
                                    class="btn-danger btn-disabled"
                                    disabled
                                    title="Les groupes système ne peuvent pas être supprimés."
                                >
                                    <i class="fa-solid fa-lock"></i>
                                    Protégé
                                </button>
                            <?php elseif ($memberCount > 0): ?>
                                <button
                                    type="button"
                                    class="btn-danger btn-disabled"
                                    disabled
                                    title="Ce groupe contient encore des membres."
                                >
                                    <i class="fa-solid fa-users"></i>
                                    Non supprimable
                                </button>
                            <?php else: ?>
                                <form
                                    method="post"
                                    action="/admin/groups/delete"
                                    class="group-delete-form"
                                    onsubmit="return confirm(
                                        'Voulez-vous vraiment supprimer le groupe « <?= htmlspecialchars(
                                            $name,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?> » ?'
                                    );"
                                >
                                    <?= $csrf ?>
                                    <input
                                        type="hidden"
                                        name="id_group"
                                        value="<?= $idGroup ?>"
                                    >
                                    <button
                                        type="submit"
                                        class="btn-danger"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                        Supprimer
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>