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
namespace BelCMS\Core;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

final class Pagination
{
    private int $currentPage;
    private int $perPage;
    private int $total;
    private int $totalPages;

    public function __construct(
        int $total,
        int $perPage = 10,
        int $currentPage = 1
    ) {
        $this->total = max(0, $total);
        $this->perPage = max(1, $perPage);
        $this->currentPage = max(1, $currentPage);

        $this->totalPages = (int) ceil(
            $this->total / $this->perPage
        );

        if (
            $this->totalPages > 0
            && $this->currentPage > $this->totalPages
        ) {
            $this->currentPage = $this->totalPages;
        }
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function totalPages(): int
    {
        return $this->totalPages;
    }

    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function hasPrevious(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    /**
     * Retourne l'URL d'une page
     */
    public function url(
        string $baseUrl,
        int $page
    ): string {
        $page = max(1, $page);

        return $baseUrl
            . '?page='
            . $page;
    }

    /**
     * Génère automatiquement la pagination HTML
     */
    public function render(
        string $baseUrl,
        string $class = 'pagination'
    ): string {
        if ($this->totalPages <= 1) {
            return '';
        }

        $html = '<nav class="' . htmlspecialchars($class) . '">';

        if ($this->hasPrevious()) {
            $html .= sprintf(
                '<a href="%s" class="pagination-link">← Précédent</a>',
                htmlspecialchars(
                    $this->url(
                        $baseUrl,
                        $this->currentPage - 1
                    )
                )
            );
        }

        for (
            $page = 1;
            $page <= $this->totalPages;
            $page++
        ) {
            $active = $page === $this->currentPage
                ? ' active'
                : '';

            $html .= sprintf(
                '<a href="%s" class="pagination-link%s">%d</a>',
                htmlspecialchars(
                    $this->url(
                        $baseUrl,
                        $page
                    )
                ),
                $active,
                $page
            );
        }

        if ($this->hasNext()) {
            $html .= sprintf(
                '<a href="%s" class="pagination-link">Suivant →</a>',
                htmlspecialchars(
                    $this->url(
                        $baseUrl,
                        $this->currentPage + 1
                    )
                )
            );
        }

        $html .= '</nav>';

        return $html;
    }
}