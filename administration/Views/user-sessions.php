<?php

declare(strict_types=1);

$user = $user ?? null;
$sessions = $sessions ?? [];

$success = $_GET['success'] ?? null;
$error   = $_GET['error'] ?? null;

$username = '';

if (is_object($user) && isset($user->username)) {
    $username = (string)$user->username;
}

?>

<div class="admin-page">

    <div class="admin-page-header">

        <div>
            <span class="admin-welcome-label">
                ADMINISTRATION
            </span>

            <h1>
                Sessions utilisateur
            </h1>

            <p>
                Sessions actives du compte
                <strong>
                    <?= htmlspecialchars(
                        $username,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
            </p>
        </div>

        <div class="admin-page-header-icon">
            <i class="fa-solid fa-laptop"></i>
        </div>

    </div>


    <?php if ($success === 'session_deleted'): ?>

        <div class="admin-alert admin-alert-success">
            <i class="fa-solid fa-circle-check"></i>
            La session a été déconnectée.
        </div>

    <?php elseif ($error === 'session_not_found'): ?>

        <div class="admin-alert admin-alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            Cette session n'existe plus.
        </div>

    <?php elseif ($error === 'session_delete'): ?>

        <div class="admin-alert admin-alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            Impossible de supprimer cette session.
        </div>

    <?php endif; ?>


    <div class="admin-card">

        <div class="admin-section-header">

            <div>
                <h2>
                    <i class="fa-solid fa-users-viewfinder"></i>
                    Sessions actives
                </h2>

                <p>
                    <?= count($sessions) ?>
                    session(s) enregistrée(s).
                </p>
            </div>

        </div>


        <?php if (empty($sessions)): ?>

            <div class="admin-empty-state">

                <i class="fa-solid fa-circle-info"></i>

                <p>
                    Aucune session active pour cet utilisateur.
                </p>

            </div>

        <?php else: ?>

            <div class="admin-sessions-list">

                <?php foreach ($sessions as $session): ?>

                    <?php
                    $sessionId = (string)(
                        $session->session_id ?? ''
                    );

                    $ip = (string)(
                        $session->ip ?? ''
                    );

                    $userAgent = (string)(
                        $session->user_agent ?? ''
                    );

                    $createdAt = (string)(
                        $session->created_at ?? ''
                    );

                    $lastActivity = (string)(
                        $session->last_activity ?? ''
                    );

                    $expiresAt = (string)(
                        $session->expires_at ?? ''
                    );
                    ?>

                    <div class="admin-session-card">

                        <div class="admin-session-icon">
                            <i class="fa-solid fa-desktop"></i>
                        </div>


                        <div class="admin-session-content">

                            <div class="admin-session-title">

                                Session

                                <?php if (!empty($session->is_current)): ?>

                                    <span class="admin-status admin-status-success">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Session courante
                                    </span>

                                <?php endif; ?>

                            </div>


                            <div class="admin-session-info">

                                <div>
                                    <strong>
                                        Adresse IP
                                    </strong>

                                    <span>
                                        <?= htmlspecialchars(
                                            $ip,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </div>


                                <div>
                                    <strong>
                                        Créée le
                                    </strong>

                                    <span>
                                        <?= htmlspecialchars(
                                            $createdAt,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </div>


                                <div>
                                    <strong>
                                        Dernière activité
                                    </strong>

                                    <span>
                                        <?= htmlspecialchars(
                                            $lastActivity,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </div>


                                <div>
                                    <strong>
                                        Expire le
                                    </strong>

                                    <span>
                                        <?= htmlspecialchars(
                                            $expiresAt,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>
                                </div>


                                <div class="admin-session-user-agent">

                                    <strong>
                                        Navigateur
                                    </strong>

                                    <span>
                                        <?= htmlspecialchars(
                                            $userAgent,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </span>

                                </div>

                            </div>

                        </div>


                        <?php if ($sessionId !== ''): ?>

                            <form
                                method="post"
                                action="/admin/users/sessions/delete"
                                onsubmit="return confirm(
                                    'Voulez-vous vraiment déconnecter cette session ?'
                                );"
                            >

                                <?= csrf_field(); ?>

                                <input
                                    type="hidden"
                                    name="hash_key"
                                    value="<?= htmlspecialchars(
                                        (string)$user->hash_key,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="session_id"
                                    value="<?= htmlspecialchars(
                                        $sessionId,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <button
                                    type="submit"
                                    class="admin-action-button admin-action-danger"
                                    title="Déconnecter"
                                >
                                    <i class="fa-solid fa-power-off"></i>
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


        <div class="admin-form-actions">

            <a
                href="/admin/users/edit?hash_key=<?= urlencode(
                    (string)$user->hash_key
                ) ?>"
                class="admin-button"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Retour à l'utilisateur
            </a>

        </div>

    </div>

</div>