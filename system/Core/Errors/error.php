<?php

declare(strict_types=1);

$errorCode = (int) ($code ?? 500);
$errorTitle = (string) ($title ?? 'Erreur');
$errorMessage = (string) ($message ?? 'Une erreur est survenue.');
$codeTitle = (string) ($codeTitle ?? 'Une erreur est survenue');

$backUrl = (string) ($backUrl ?? '/');
$backLabel = (string) ($backLabel ?? 'Retour');

$errorIcon = match ($errorCode) {
    400 => 'fa-triangle-exclamation',
    403 => 'fa-shield-halved',
    404 => 'fa-map-location-dot',
    405 => 'fa-ban',
    419 => 'fa-clock',
    429 => 'fa-gauge-high',
    500 => 'fa-server',
    503 => 'fa-screwdriver-wrench',
    default => 'fa-circle-exclamation',
};

?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($errorCode . ' - ' . $errorTitle, ENT_QUOTES, 'UTF-8') ?>
        - Bel-CMS
    </title>

    <link
        rel="stylesheet"
        href="/assets/plugins/fontawesome/css/all.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        body {
            font-family:
                Inter,
                "Segoe UI",
                Arial,
                sans-serif;

            background:
                radial-gradient(
                    circle at 50% 0%,
                    #102c52 0%,
                    #07111f 45%,
                    #030812 100%
                );

            color: #ffffff;
        }

        .error-page {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 30px;

            position: relative;
            overflow: hidden;
        }

        .error-background {
            position: absolute;
            inset: 0;

            overflow: hidden;

            pointer-events: none;
        }

        .error-background span {
            position: absolute;

            display: block;

            border: 1px solid rgba(0, 153, 255, 0.12);

            border-radius: 50%;

            animation: errorPulse 8s infinite ease-in-out;
        }

        .error-background span:nth-child(1) {
            width: 500px;
            height: 500px;

            top: -250px;
            left: -150px;
        }

        .error-background span:nth-child(2) {
            width: 700px;
            height: 700px;

            right: -350px;
            bottom: -350px;

            animation-delay: 2s;
        }

        .error-background span:nth-child(3) {
            width: 300px;
            height: 300px;

            right: 15%;
            top: 10%;

            animation-delay: 4s;
        }

        .error-card {
            position: relative;
            z-index: 2;

            width: 100%;
            max-width: 650px;

            padding: 55px 45px;

            text-align: center;

            background:
                linear-gradient(
                    145deg,
                    rgba(12, 29, 52, 0.96),
                    rgba(5, 14, 27, 0.96)
                );

            border: 1px solid rgba(0, 153, 255, 0.25);

            border-radius: 20px;

            box-shadow:
                0 30px 80px rgba(0, 0, 0, 0.55),
                0 0 40px rgba(0, 140, 255, 0.08);

            backdrop-filter: blur(12px);
        }

        .error-icon {
            width: 82px;
            height: 82px;

            margin: 0 auto 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            color: #4db8ff;

            font-size: 34px;

            background: rgba(0, 140, 255, 0.08);

            border: 1px solid rgba(0, 153, 255, 0.35);

            box-shadow:
                0 0 30px rgba(0, 140, 255, 0.15);
        }

        .error-code {
            font-size: 76px;

            line-height: 1;

            font-weight: 800;

            letter-spacing: 4px;

            color: #3daeff;

            text-shadow:
                0 0 25px rgba(0, 153, 255, 0.35);
        }

        .error-card h1 {
            margin: 15px 0 8px;

            font-size: 27px;

            font-weight: 700;
        }

        .error-card h2 {
            margin: 0 0 12px;

            font-size: 18px;

            color: #9fcdf0;

            font-weight: 500;
        }

        .error-card p {
            margin: 0 auto;

            max-width: 500px;

            color: #a9b9ca;

            line-height: 1.7;

            font-size: 15px;
        }

        .error-separator {
            width: 70px;
            height: 2px;

            margin: 20px auto;

            background: #2196f3;

            box-shadow:
                0 0 12px rgba(33, 150, 243, 0.7);
        }

        .error-actions {
            display: flex;

            justify-content: center;

            gap: 12px;

            margin-top: 30px;

            flex-wrap: wrap;
        }

        .error-button {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            gap: 9px;

            min-width: 190px;

            padding: 12px 20px;

            border-radius: 9px;

            color: #ffffff;

            text-decoration: none;

            background:
                linear-gradient(
                    135deg,
                    #087ed4,
                    #1555a6
                );

            border: 1px solid rgba(80, 180, 255, 0.35);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                filter 0.2s ease;
        }

        .error-button:hover {
            transform: translateY(-2px);

            filter: brightness(1.15);

            box-shadow:
                0 8px 25px rgba(0, 120, 255, 0.25);
        }

        .error-button.secondary {
            background:
                rgba(255, 255, 255, 0.04);

            border-color:
                rgba(255, 255, 255, 0.12);

            color: #c8d4df;
        }

        .error-button.secondary:hover {
            background:
                rgba(255, 255, 255, 0.08);
        }

        .error-footer {
            margin-top: 35px;

            font-size: 12px;

            color: #60758b;

            letter-spacing: 1px;
        }

        @keyframes errorPulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.4;
            }

            50% {
                transform: scale(1.08);
                opacity: 0.8;
            }
        }

        @media (max-width: 600px) {

            .error-page {
                padding: 15px;
            }

            .error-card {
                padding: 40px 25px;
            }

            .error-code {
                font-size: 58px;
            }

            .error-card h1 {
                font-size: 23px;
            }

            .error-actions {
                flex-direction: column;
            }

            .error-button {
                width: 100%;
            }
        }

    </style>

</head>

<body>

<div class="error-page">

    <div class="error-background">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <main class="error-card">

        <div class="error-icon">
            <i class="fa-solid <?= htmlspecialchars($errorIcon, ENT_QUOTES, 'UTF-8') ?>"></i>
        </div>

        <div class="error-code">
            <?= htmlspecialchars((string) $errorCode, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <h1>
            <?= htmlspecialchars($errorTitle, ENT_QUOTES, 'UTF-8') ?>
        </h1>

        <div class="error-separator"></div>

        <h2>
            <?= htmlspecialchars($codeTitle, ENT_QUOTES, 'UTF-8') ?>
        </h2>

        <p>
            <?= nl2br(
                htmlspecialchars(
                    $errorMessage,
                    ENT_QUOTES,
                    'UTF-8'
                )
            ) ?>
        </p>

        <div class="error-actions">

            <a
                href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>"
                class="error-button"
            >
                <i class="fa-solid fa-arrow-left"></i>

                <?= htmlspecialchars($backLabel, ENT_QUOTES, 'UTF-8') ?>
            </a>

            <a
                href="/"
                class="error-button secondary"
            >
                <i class="fa-solid fa-house"></i>

                Accueil
            </a>

        </div>

        <div class="error-footer">
            Bel-CMS V5
        </div>

    </main>

</div>

</body>

</html>