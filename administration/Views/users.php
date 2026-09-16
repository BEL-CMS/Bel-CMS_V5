<?php

declare(strict_types=1);

$users      = $users ?? [];
$search     = $search ?? '';
$page       = (int)($page ?? 1);
$totalPages = (int)($totalPages ?? 1);
$totalUsers = (int)($totalUsers ?? 0);

?>
<?php

$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

?>

<?php if ($success === 'deleted'): ?>

    <div class="admin-user-alert admin-user-alert-success">

        <i class="fa-solid fa-circle-check"></i>

        <div>
            L'utilisateur a été supprimé avec succès.
        </div>

    </div>

<?php endif; ?>


<?php if ($error === 'self-delete'): ?>

    <div class="admin-user-alert admin-user-alert-danger">

        <i class="fa-solid fa-triangle-exclamation"></i>

        <div>
            Vous ne pouvez pas supprimer votre propre compte.
        </div>

    </div>

<?php elseif ($error === 'not-found'): ?>

    <div class="admin-user-alert admin-user-alert-danger">

        <i class="fa-solid fa-triangle-exclamation"></i>

        <div>
            L'utilisateur demandé est introuvable.
        </div>

    </div>

<?php elseif ($error === 'delete'): ?>

    <div class="admin-user-alert admin-user-alert-danger">

        <i class="fa-solid fa-triangle-exclamation"></i>

        <div>
            Impossible de supprimer cet utilisateur.
        </div>

    </div>

<?php endif; ?>
<div class="admin-page">

    <div class="admin-page-header">

        <div>
            <span class="admin-welcome-label">
                ADMINISTRATION
            </span>

            <h1>
                Utilisateurs
            </h1>

            <p>
                Gestion des comptes utilisateurs de Bel-CMS.
            </p>
        </div>

        <div class="admin-page-header-icon">
            <i class="fa-solid fa-users"></i>
        </div>

    </div>


    <div class="admin-card admin-users-toolbar">

        <div class="admin-users-total">
            <i class="fa-solid fa-users"></i>

            <div>
                <strong>
                    <?= number_format(
                        $totalUsers,
                        0,
                        ',',
                        ' '
                    ) ?>
                </strong>

                <span>
                    utilisateurs
                </span>
            </div>
        </div>


        <form
            method="get"
            action="/admin/users"
            class="admin-users-search"
        >

            <div class="admin-search-input">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="search"
                    name="search"
                    value="<?= htmlspecialchars(
                        $search,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    placeholder="Rechercher un utilisateur..."
                >

            </div>

            <button
                type="submit"
                class="admin-button"
            >
                <i class="fa-solid fa-search"></i>
                Rechercher
            </button>

            <?php if ($search !== ''): ?>

                <a
                    href="/admin/users"
                    class="admin-button admin-button-secondary"
                >
                    <i class="fa-solid fa-xmark"></i>
                    Effacer
                </a>

            <?php endif; ?>

        </form>

    </div>


    <div class="admin-card admin-users-table-card">

        <?php if (empty($users)): ?>

            <div class="admin-empty-state">

                <div class="admin-empty-icon">
                    <i class="fa-solid fa-user-slash"></i>
                </div>

                <h2>
                    Aucun utilisateur
                </h2>

                <p>
                    Aucun compte ne correspond à votre recherche.
                </p>

            </div>

        <?php else: ?>

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Utilisateur
                            </th>

                            <th>
                                E-mail
                            </th>

                            <th>
                                Statut
                            </th>

                            <th>
                                Administration
                            </th>

                            <th>
                                IP
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($users as $item): ?>

                        <?php

                        $username = (string)(
                            $item->username ?? ''
                        );

                        $email = (string)(
                            $item->email ?? ''
                        );

                        $ip = (string)(
                            $item->ip ?? ''
                        );

                        $valid = !empty(
                            $item->valid ?? false
                        );

                        $isAdmin =
                            !empty($item->admin)
                            || !empty($item->is_admin);

                        ?>

                        <tr>

                            <td>
                                <span class="admin-user-id">
                                    #<?= (int)($item->id ?? 0) ?>
                                </span>
                            </td>


                            <td>

                                <div class="admin-user-name">

                                    <span class="admin-user-avatar">
                                        <i class="fa-solid fa-user"></i>
                                    </span>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $username,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>

                                </div>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $email,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>

                                <?php if ($valid): ?>

                                    <span class="admin-status admin-status-success">
                                        <i class="fa-solid fa-circle"></i>
                                        Actif
                                    </span>

                                <?php else: ?>

                                    <span class="admin-status admin-status-danger">
                                        <i class="fa-solid fa-circle"></i>
                                        Inactif
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <?php if ($isAdmin): ?>

                                    <span class="admin-status admin-status-admin">
                                        <i class="fa-solid fa-shield-halved"></i>
                                        Administrateur
                                    </span>

                                <?php else: ?>

                                    <span class="admin-status admin-status-neutral">
                                        <i class="fa-solid fa-user"></i>
                                        Utilisateur
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <span class="admin-user-ip">

                                    <?= $ip !== ''
                                        ? htmlspecialchars(
                                            $ip,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )
                                        : '—'
                                    ?>

                                </span>

                            </td>


                            <td>

                                <div class="admin-user-actions">

<a
    href="/admin/users/view?hash_key=<?= urlencode(
        (string)($item->hash_key ?? '')
    ) ?>"
    class="admin-action-button"
    title="Voir"
>
    <i class="fa-solid fa-eye"></i>
</a>

<a
    href="/admin/users/edit?hash_key=<?= urlencode(
        (string)($item->hash_key ?? '')
    ) ?>"
    class="admin-action-button"
    title="Modifier"
>
    <i class="fa-solid fa-pen"></i>
</a>

<form
    action="/admin/users/delete"
    method="post"
    class="admin-delete-user-form"
    onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');"
>
    <?= csrf_field(); ?>

    <input
        type="hidden"
        name="hash_key"
        value="<?= htmlspecialchars(
            (string)($item->hash_key ?? ''),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <button
        type="submit"
        class="admin-action-button admin-action-danger"
        title="Supprimer"
    >
        <i class="fa-solid fa-trash"></i>
    </button>
</form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


            <?php if ($totalPages > 1): ?>

                <div class="admin-pagination">

                    <?php if ($page > 1): ?>

                        <a
                            href="/admin/users?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>"
                            class="admin-page-button"
                        >
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>

                    <?php endif; ?>


                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                        <a
                            href="/admin/users?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                            class="
                                admin-page-button
                                <?= $i === $page
                                    ? 'active'
                                    : ''
                                ?>
                            "
                        >
                            <?= $i ?>
                        </a>

                    <?php endfor; ?>


                    <?php if ($page < $totalPages): ?>

                        <a
                            href="/admin/users?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>"
                            class="admin-page-button"
                        >
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>