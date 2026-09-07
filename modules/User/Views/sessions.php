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

/**
 * @var array $sessions
 * @var string $currentSessionId
 * @var string|null $success
 * @var string|null $error
 */

$sessions = is_array($sessions) ? $sessions : [];
?>

<div class="user-page">

    <div class="user-page-header">
        <div>
            <h1>
                <i class="fa-solid fa-laptop"></i>
                Sessions actives
            </h1>

            <p>
                Gérez les appareils actuellement connectés à votre compte.
            </p>
        </div>

        <a href="/user/security" class="user-btn user-btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Retour
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="user-alert user-alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="user-alert user-alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="user-card">

        <div class="user-card-header">
            <div>
                <h2>
                    <i class="fa-solid fa-shield-halved"></i>
                    Appareils connectés
                </h2>

                <p>
                    <?= count($sessions) ?> session(s) active(s)
                </p>
            </div>

            <?php
            $otherSessions = 0;

            foreach ($sessions as $session) {
                if (empty($session->is_current)) {
                    $otherSessions++;
                }
            }
            ?>

            <?php if ($otherSessions > 0): ?>
                <form method="post" action="/user/security/sessions">
                    <input type="hidden" name="action" value="logout-others">

                    <button
                        type="submit"
                        class="user-btn user-btn-danger"
                        onclick="return confirm('Déconnecter toutes les autres sessions ?');"
                    >
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Déconnecter les autres
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div class="user-sessions-list">

            <?php if (empty($sessions)): ?>

                <div class="user-empty">
                    <i class="fa-solid fa-circle-info"></i>
                    Aucune session enregistrée.
                </div>

            <?php else: ?>

                <?php foreach ($sessions as $session): ?>

                    <div class="user-session-item <?= !empty($session->is_current) ? 'current' : '' ?>">

                        <div class="user-session-icon">
                            <i class="fa-solid fa-desktop"></i>
                        </div>

                        <div class="user-session-info">

                            <div class="user-session-title">

                                <?php if (!empty($session->is_current)): ?>
                                    <strong>
                                        Session actuelle
                                    </strong>

                                    <span class="user-session-badge">
                                        <i class="fa-solid fa-circle"></i>
                                        Connectée
                                    </span>

                                <?php else: ?>

                                    <strong>
                                        Session active
                                    </strong>

                                <?php endif; ?>

                            </div>

                            <div class="user-session-details">

                                <span>
                                    <i class="fa-solid fa-network-wired"></i>
                                    <?= htmlspecialchars(
                                        (string)($session->ip ?? 'Inconnue'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                                <span>
                                    <i class="fa-solid fa-clock"></i>
                                    <?= htmlspecialchars(
                                        (string)($session->last_activity ?? '-'),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            </div>

                            <?php if (!empty($session->user_agent)): ?>

                                <div class="user-session-agent">
                                    <i class="fa-solid fa-globe"></i>

                                    <?= htmlspecialchars(
                                        (string)$session->user_agent,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </div>

</div>