<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/

declare(strict_types=1);

use BelCMS\Admin\News\Controller as NewsController;

return [
    [
        'method' => 'GET',
        'path' => '/admin/news',
        'handler' => [NewsController::class, 'index'],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/news/create',
        'handler' => [NewsController::class, 'create'],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/news/create',
        'handler' => [NewsController::class, 'create'],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/news/edit',
        'handler' => [NewsController::class, 'edit'],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/news/edit',
        'handler' => [NewsController::class, 'edit'],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/news/delete',
        'handler' => [NewsController::class, 'delete'],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/news/status',
        'handler' => [NewsController::class, 'status'],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/news/categories',
        'handler' => [NewsController::class, 'categories'],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/news/categories',
        'handler' => [NewsController::class, 'categories'],
    ],
];
