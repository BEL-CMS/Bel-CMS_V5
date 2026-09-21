<?php
declare(strict_types=1);

/**
 * @var object $group
 * @var array $users
 * @var string $search
 * @var string $csrf
 * @var string|null $error
 */

$groupName = htmlspecialchars(
    (string) ($group->name ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

$idGroup = (int) ($group->id_group ?? 0);
$searchValue = htmlspecialchars(
    (string) ($search ?? ''),
    ENT_QUOTES,
    'UTF-8'
);
?>

<div class="group-page">

    <div class="group-page-header">
        <div class="group-page-header-main">
            <a
                href="/admin/groups/members?id_group=<?= $idGroup ?>"
                class="btn btn-secondary"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Retour aux membres
            </a>

            <div class="group-page-title">
                <span class="group-page-eyebrow">Gestion du groupe</span>
                <h1>
                    <i class="fa-solid fa-user-plus"></i>
                    Ajouter un membre
                </h1>
                <p>
                    Ajouter un utilisateur au groupe
                    <strong><?= $groupName ?></strong>.
                </p>
            </div>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="group-alert group-alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    <?php endif; ?>

    <div class="group-add-panel">

        <form
            method="get"
            action="/admin/groups/members/add"
            class="group-add-search"
        >
            <input
                type="hidden"
                name="id_group"
                value="<?= $idGroup ?>"
            >

            <div class="group-search-field">
                <label for="group-user-search">
                    Rechercher un utilisateur
                </label>

                <div class="group-search-control">
                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input
                        type="search"
                        id="group-user-search"
                        name="search"
                        value="<?= $searchValue ?>"
                        placeholder="Pseudo ou adresse e-mail..."
                        autocomplete="off"
                    >

                    <?php if ($searchValue !== ''): ?>
                        <a
                            href="/admin/groups/members/add?id_group=<?= $idGroup ?>"
                            class="group-search-clear"
                            title="Effacer la recherche"
                            aria-label="Effacer la recherche"
                        >
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i>
                Rechercher
            </button>
        </form>

        <div class="group-add-info">
            <i class="fa-solid fa-circle-info"></i>

            <?php if ($searchValue !== ''): ?>
                Utilisateurs disponibles correspondant à
                <strong><?= $searchValue ?></strong>.
            <?php else: ?>
                Sélectionnez un utilisateur parmi les membres disponibles.
            <?php endif; ?>
        </div>

        <?php if (!empty($users)): ?>

            <div class="group-add-user-list">

                <?php foreach ($users as $user): ?>
                    <?php
                    $hashKey = htmlspecialchars(
                        (string) ($user->hash_key ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    $username = htmlspecialchars(
                        (string) ($user->username ?? 'Utilisateur'),
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    $email = htmlspecialchars(
                        (string) ($user->email ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    $initial = mb_strtoupper(
                        mb_substr(
                            (string) ($user->username ?? '?'),
                            0,
                            1
                        )
                    );
                    ?>

                    <article class="group-add-user-card">

                        <div class="group-add-user-avatar">
                            <?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <div class="group-add-user-content">
                            <div class="group-add-user-name">
                                <?= $username ?>
                            </div>

                            <?php if ($email !== ''): ?>
                                <div class="group-add-user-email">
                                    <i class="fa-solid fa-envelope"></i>
                                    <?= $email ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="group-add-user-actions">

                            <a
                                href="/admin/users/edit?hash_key=<?= $hashKey ?>"
                                class="btn btn-secondary btn-small"
                            >
                                <i class="fa-solid fa-eye"></i>
                                Voir
                            </a>

                            <form
                                method="post"
                                action="/admin/groups/members/add"
                                onsubmit="return confirm('Ajouter cet utilisateur au groupe ?');"
                            >
                                <?= $csrf ?>

                                <input
                                    type="hidden"
                                    name="id_group"
                                    value="<?= $idGroup ?>"
                                >

                                <input
                                    type="hidden"
                                    name="hash_key"
                                    value="<?= $hashKey ?>"
                                >

                                <input
                                    type="hidden"
                                    name="search"
                                    value="<?= $searchValue ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-success btn-small"
                                >
                                    <i class="fa-solid fa-user-plus"></i>
                                    Ajouter
                                </button>
                            </form>

                        </div>

                    </article>
                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="group-empty-state">
                <div class="group-empty-icon">
                    <i class="fa-solid fa-user-slash"></i>
                </div>

                <?php if ($searchValue !== ''): ?>
                    <h3>Aucun utilisateur trouvé</h3>
                    <p>
                        Aucun utilisateur disponible ne correspond à votre recherche.
                    </p>
                <?php else: ?>
                    <h3>Aucun utilisateur disponible</h3>
                    <p>
                        Tous les utilisateurs sont déjà membres de ce groupe,
                        ou aucun utilisateur n'est disponible.
                    </p>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </div>

</div>
