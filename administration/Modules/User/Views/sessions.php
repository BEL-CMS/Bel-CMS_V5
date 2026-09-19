<?php

declare(strict_types=1);

$sessions   = $sessions ?? [];
$targetUser = $targetUser ?? null;
$hashKey    = (string) ($hashKey ?? '');

$username = is_object($targetUser)
    ? (string) ($targetUser->username ?? '')
    : '';

?>

<div class="admin-user-sessions">

    <div class="admin-users-header">

        <h1>
            <i class="fa-solid fa-laptop"></i>
            Sessions utilisateur
        </h1>

        <p>
            Sessions actuellement enregistrées pour
            <strong>
                <?= htmlspecialchars(
                    $username,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>
        </p>

    </div>


    <div class="admin-user-session-actions">

        <a
            href="/admin/users"
            class="admin-user-button secondary"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Retour
        </a>

        <?php if ($sessions !== []): ?>

            <form
                method="post"
                action="/admin/users/sessions/delete-all"
                onsubmit="return confirm('Voulez-vous vraiment déconnecter toutes les sessions de cet utilisateur ?');"
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
                    class="admin-user-button danger"
                >
                    <i class="fa-solid fa-power-off"></i>
                    Déconnecter toutes les sessions
                </button>

            </form>

        <?php endif; ?>

    </div>


    <div class="admin-user-sessions-list">

        <?php if ($sessions === []): ?>

            <div class="admin-users-empty">

                <i class="fa-solid fa-circle-check"></i>

                <p>
                    Aucune session active pour cet utilisateur.
                </p>

            </div>

        <?php else: ?>

            <?php foreach ($sessions as $session): ?>

                <div class="admin-user-session">

                    <div class="admin-user-session-icon">
                        <i class="fa-solid fa-desktop"></i>
                    </div>

                    <div class="admin-user-session-info">

                        <strong>
                            Session utilisateur
                        </strong>

                        <span>
                            IP :
                            <?= htmlspecialchars(
                                (string) ($session->ip ?? '-'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                    </div>

                    <div class="admin-user-session-id">

                        <small>
                            <?= htmlspecialchars(
                                (string) ($session->session_id ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </small>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>