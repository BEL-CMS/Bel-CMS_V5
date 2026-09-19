<?php

declare(strict_types=1);

namespace BelCMS\Core\Errors;

final class ErrorPage
{
    public function show(
        int $code,
        string $title,
        string $message,
        string $backUrl = '/',
        string $backLabel = 'Retour'
    ): never {
        http_response_code($code);

        $codeTitles = [
            400 => 'Requête invalide',
            403 => 'Accès refusé',
            404 => 'Page introuvable',
            405 => 'Méthode non autorisée',
            419 => 'Session expirée',
            429 => 'Trop de requêtes',
            500 => 'Erreur serveur',
            503 => 'Service indisponible',
        ];

        $codeTitle = $codeTitles[$code]
            ?? 'Une erreur est survenue';

        $view = __DIR__ . DIRECTORY_SEPARATOR . 'error.php';

        if (!is_file($view)) {
            exit($message);
        }

        require $view;

        exit;
    }
}