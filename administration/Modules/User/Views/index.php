<?php

declare(strict_types=1);

$users  = $users ?? [];
$page   = max(1, (int) ($page ?? 1));
$pages  = max(1, (int) ($pages ?? 1));
$total  = max(0, (int) ($total ?? 0));
$search = (string) ($search ?? '');

?>

<div class="admin-users">

    <!-- =====================================================
         En-tête
         ===================================================== -->

    <div class="admin-users-header">

        <h1>
            <i class="fa-solid fa-users"></i>
            Utilisateurs
        </h1>

        <p>
            Gestion des comptes utilisateurs de Bel-CMS.
            <?= $total ?> utilisateur<?= $total > 1 ? 's' : '' ?> enregistré<?= $total > 1 ? 's' : '' ?>.
        </p>

    </div>


    <!-- =====================================================
         Recherche
         ===================================================== -->

    <form
        method="get"
        action="/admin/users"
        class="admin-users-search"
    >

        <input
            type="search"
            name="search"
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

            <a
                href="/admin/users"
                class="admin-user-action"
                title="Réinitialiser la recherche"
            >
                <i class="fa-solid fa-xmark"></i>
            </a>

        <?php endif; ?>

    </form>


    <!-- =====================================================
         Tableau
         ===================================================== -->

    <div class="admin-users-table">

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


        <?php if ($users === []): ?>

            <!-- Aucun résultat -->

            <div class="admin-users-empty">

                <i class="fa-solid fa-user-slash"></i>

                <div>
                    Aucun utilisateur trouvé.
                </div>

                <?php if ($search !== ''): ?>

                    <small>
                        Aucun résultat pour :
                        <strong>
                            <?= htmlspecialchars(
                                $search,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>
                    </small>

                <?php endif; ?>

            </div>

        <?php else: ?>


            <!-- =================================================
                 Utilisateurs
                 ================================================= -->

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

                <div class="admin-user-row">

                    <!-- Utilisateur -->

                    <div class="admin-user-name">

                        <div class="admin-user-avatar">

                            <i class="fa-solid fa-user"></i>

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

                        <?php if ($valid === 1): ?>

                            <span class="admin-user-status">
                                Actif
                            </span>

                        <?php else: ?>

                            <span class="admin-user-status">
                                Inactif
                            </span>

                        <?php endif; ?>

                    </div>


                    <!-- 2FA -->

                    <div>

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

                    </div>


                    <!-- Actions -->

                    <div class="admin-user-actions">

                        <?php if ($hashKey !== ''): ?>

                            <!-- Modifier -->

                            <a
                                href="/admin/users/edit?hash_key=<?= urlencode($hashKey) ?>"
                                class="admin-user-action"
                                title="Modifier l'utilisateur"
                            >
                                <i class="fa-solid fa-pen"></i>
                            </a>


                            <!-- Sessions -->

                            <a
                                href="/admin/users/sessions?hash_key=<?= urlencode($hashKey) ?>"
                                class="admin-user-action"
                                title="Sessions"
                            >
                                <i class="fa-solid fa-laptop"></i>
                            </a>


                            <!-- Supprimer -->

                            <form
                                method="post"
                                action="/admin/users/delete"
                                style="display: inline;"
                                onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');"
                            >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars(
                                        csrf_token(),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="hash_key"
                                    value="<?= htmlspecialchars(
                                        $hashKey,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

<!-- Supprimer -->

<form
    method="post"
    action="/admin/users/delete"
    style="display: inline;"
    onsubmit="return confirm(
        'ATTENTION !\n\n' +
        'Vous êtes sur le point de supprimer définitivement cet utilisateur et ses données associées.\n\n' +
        'Cette action est irréversible.\n\n' +
        'Voulez-vous vraiment continuer ?'
    );"
>

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars(
            csrf_token(),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <input
        type="hidden"
        name="hash_key"
        value="<?= htmlspecialchars(
            $hashKey,
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <button
        type="submit"
        class="admin-user-action delete"
        title="Supprimer l'utilisateur"
    >
        <i class="fa-solid fa-trash"></i>
    </button>

</form>

                            </form>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>


    <!-- =====================================================
         Pagination
         ===================================================== -->

    <?php if ($pages > 1): ?>

        <div class="admin-users-pagination">

            <?php if ($page > 1): ?>

                <a
                    href="/admin/users?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>"
                    title="Page précédente"
                >
                    <i class="fa-solid fa-chevron-left"></i>
                </a>

            <?php endif; ?>


            <?php for ($i = 1; $i <= $pages; $i++): ?>

                <?php if ($i === $page): ?>

                    <span class="active">
                        <?= $i ?>
                    </span>

                <?php else: ?>

                    <a
                        href="/admin/users?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                    >
                        <?= $i ?>
                    </a>

                <?php endif; ?>

            <?php endfor; ?>


            <?php if ($page < $pages): ?>

                <a
                    href="/admin/users?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>"
                    title="Page suivante"
                >
                    <i class="fa-solid fa-chevron-right"></i>
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>