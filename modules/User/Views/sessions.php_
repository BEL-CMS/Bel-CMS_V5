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
 * @var array       $sessions
 * @var string      $currentSessionId
 * @var string|null $success
 * @var string|null $error
 */

$sessions = is_array($sessions) ? $sessions : [];

/*
 * Analyse simple du User-Agent
 */
function belcmsSessionBrowser(?string $userAgent): string
{
    $userAgent = (string)$userAgent;

    if (stripos($userAgent, 'Firefox') !== false) {
        preg_match('/Firefox\/([0-9.]+)/i', $userAgent, $matches);

        return 'Firefox' . (
            isset($matches[1])
                ? ' ' . $matches[1]
                : ''
        );
    }

    if (stripos($userAgent, 'Edg/') !== false) {
        preg_match('/Edg\/([0-9.]+)/i', $userAgent, $matches);

        return 'Edge' . (
            isset($matches[1])
                ? ' ' . $matches[1]
                : ''
        );
    }

    if (stripos($userAgent, 'Chrome/') !== false) {
        preg_match('/Chrome\/([0-9.]+)/i', $userAgent, $matches);

        return 'Chrome' . (
            isset($matches[1])
                ? ' ' . $matches[1]
                : ''
        );
    }

    if (stripos($userAgent, 'Safari/') !== false) {
        return 'Safari';
    }

    return 'Navigateur inconnu';
}


function belcmsSessionOs(?string $userAgent): string
{
    $userAgent = (string)$userAgent;

    if (stripos($userAgent, 'Windows NT 10.0') !== false) {
        return 'Windows 10';
    }

    if (stripos($userAgent, 'Windows NT 6.3') !== false) {
        return 'Windows 8.1';
    }

    if (stripos($userAgent, 'Windows NT 6.2') !== false) {
        return 'Windows 8';
    }

    if (stripos($userAgent, 'Windows NT 6.1') !== false) {
        return 'Windows 7';
    }

    if (stripos($userAgent, 'Mac OS X') !== false) {
        return 'macOS';
    }

    if (stripos($userAgent, 'Android') !== false) {
        return 'Android';
    }

    if (
        stripos($userAgent, 'iPhone') !== false
        || stripos($userAgent, 'iPad') !== false
    ) {
        return 'iOS';
    }

    if (stripos($userAgent, 'Linux') !== false) {
        return 'Linux';
    }

    return 'Système inconnu';
}


function belcmsSessionDeviceIcon(?string $userAgent): string
{
    $userAgent = (string)$userAgent;

    if (
        stripos($userAgent, 'Android') !== false
        || stripos($userAgent, 'iPhone') !== false
        || stripos($userAgent, 'iPad') !== false
    ) {
        return 'fa-mobile-screen-button';
    }

    return 'fa-desktop';
}

?>

<div class="user-sessions-page">

    <!-- =====================================================
         EN-TÊTE
         ===================================================== -->

    <div class="user-sessions-header">

        <div>

            <div class="user-sessions-breadcrumb">
                <span>
                    <i class="fa-solid fa-house"></i>
                </span>

                <i class="fa-solid fa-angle-right"></i>

                <span>User</span>

                <i class="fa-solid fa-angle-right"></i>

                <strong>Sessions actives</strong>
            </div>


            <h1>
                <i class="fa-solid fa-laptop"></i>
                Sessions <span>actives</span>
            </h1>

            <p>
                Gérez les appareils actuellement connectés à votre compte.
            </p>

        </div>


        <a
            href="/user/security"
            class="user-sessions-back"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Retour
        </a>

    </div>


    <!-- =====================================================
         MESSAGES
         ===================================================== -->

    <?php if (!empty($success)): ?>

        <div class="user-session-alert user-session-alert-success">
            <i class="fa-solid fa-circle-check"></i>

            <span>
                <?= htmlspecialchars(
                    $success,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        </div>

    <?php endif; ?>


    <?php if (!empty($error)): ?>

        <div class="user-session-alert user-session-alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>

            <span>
                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        </div>

    <?php endif; ?>


    <!-- =====================================================
         CONTENU
         ===================================================== -->

    <section class="user-sessions-panel">


        <!-- =================================================
             COLONNE GAUCHE
             ================================================= -->

        <aside class="user-sessions-sidebar">

            <div class="user-sessions-sidebar-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>


            <h2>
                Appareils<br>
                connectés
            </h2>


            <div class="user-sessions-sidebar-line"></div>


            <p>
                Voici la liste des appareils actuellement connectés
                à votre compte. Si vous remarquez une activité inhabituelle,
                vous pouvez déconnecter les autres sessions.
            </p>


            <div class="user-sessions-count">

                <span class="user-sessions-count-dot"></span>

                <strong>
                    <?= count($sessions) ?>
                </strong>

                session(s) active(s)

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

                <form
                    method="post"
                    action="/user/security/sessions"
                >

                    <input
                        type="hidden"
                        name="action"
                        value="logout-others"
                    >

                    <button
                        type="submit"
                        class="user-sessions-logout-all"
                        onclick="return confirm('Déconnecter toutes les autres sessions ?');"
                    >

                        <i class="fa-solid fa-right-from-bracket"></i>

                        <span>

                            <strong>
                                Déconnecter les autres
                            </strong>

                            <small>
                                Garder uniquement cette session
                            </small>

                        </span>

                    </button>

                </form>

            <?php endif; ?>

        </aside>


        <!-- =================================================
             LISTE DES SESSIONS
             ================================================= -->

        <div class="user-sessions-content">

            <?php if (empty($sessions)): ?>

                <div class="user-sessions-empty">

                    <i class="fa-solid fa-circle-info"></i>

                    <strong>
                        Aucune session active
                    </strong>

                    <span>
                        Aucune session n'est actuellement enregistrée.
                    </span>

                </div>

            <?php else: ?>

                <?php foreach ($sessions as $session): ?>

                    <?php

                    $isCurrent = !empty($session->is_current);

                    $userAgent = (string)($session->user_agent ?? '');

                    $browser = belcmsSessionBrowser($userAgent);
                    $os = belcmsSessionOs($userAgent);
                    $deviceIcon = belcmsSessionDeviceIcon($userAgent);

                    $ip = (string)($session->ip ?? 'Inconnue');

                    $lastActivity = (string)(
                        $session->last_activity ?? '-'
                    );

                    ?>

                    <article
                        class="
                            user-session-card
                            <?= $isCurrent ? 'is-current' : '' ?>
                        "
                    >

                        <div class="user-session-card-device">

                            <div class="user-session-device-icon">

                                <i class="fa-brands fa-windows"></i>

                            </div>

                            <span class="user-session-device-status"></span>

                        </div>


                        <div class="user-session-main">

                            <div class="user-session-title-row">

                                <div>

                                    <h3>

                                        <?= $isCurrent
                                            ? 'Session actuelle'
                                            : 'Session active'
                                        ?>

                                    </h3>


                                    <?php if ($isCurrent): ?>

                                        <span class="user-session-current-badge">

                                            <i class="fa-solid fa-circle"></i>

                                            Connectée

                                        </span>

                                    <?php else: ?>

                                        <span class="user-session-device-badge">

                                            <i class="fa-solid fa-desktop"></i>

                                            Appareil secondaire

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>


                            <div class="user-session-information">


                                <div class="user-session-information-item">

                                    <i class="fa-solid fa-location-dot"></i>

                                    <div>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $ip,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>

                                        <span>
                                            Adresse IP
                                        </span>

                                    </div>

                                </div>


                                <div class="user-session-information-item">

                                    <i class="fa-solid fa-calendar-days"></i>

                                    <div>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $lastActivity,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>

                                        <span>
                                            Dernière activité
                                        </span>

                                    </div>

                                </div>


                                <div class="user-session-information-item">

                                    <i class="fa-solid fa-desktop"></i>

                                    <div>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $os,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>

                                        <span>
                                            Système d'exploitation
                                        </span>

                                    </div>

                                </div>


                                <div class="user-session-information-item">

                                    <i class="fa-brands fa-firefox-browser"></i>

                                    <div>

                                        <strong>
                                            <?= htmlspecialchars(
                                                $browser,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>

                                        <span>
                                            Navigateur
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <?php if (!empty($userAgent)): ?>

                                <div class="user-session-user-agent">

                                    <i class="fa-solid fa-globe"></i>

                                    <?= htmlspecialchars(
                                        $userAgent,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </div>

                            <?php endif; ?>

                        </div>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </section>

</div>