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

use BelCMS\Admin\News\Controller as NewsController;

return [
    'GET /admin/news' => [
        NewsController::class,
        'index',
    ],
    'GET /admin/news/create' => [
        NewsController::class,
        'create',
    ],
    'POST /admin/news/create' => [
        NewsController::class,
        'create',
    ],
    'GET /admin/news/edit' => [
        NewsController::class,
        'edit',
    ],
    'POST /admin/news/edit' => [
        NewsController::class,
        'edit',
    ],
    'POST /admin/news/delete' => [
        NewsController::class,
        'delete',
    ],
    'POST /admin/news/status' => [
        NewsController::class,
        'status',
    ],
    'GET /admin/news/categories' => [
        NewsController::class,
        'categories',
    ],
    'POST /admin/news/categories' => [
        NewsController::class,
        'categories',
    ],
];