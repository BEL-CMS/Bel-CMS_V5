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

use BelCMS\Admin\User\Controller as UserController;
return [
    [
        'method' => 'GET',
        'path' => '/admin/users',
        'handler' => [
            UserController::class,
            'index',
        ],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/users/valid',
        'handler' => [
            UserController::class,
            'valid',
        ],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/users/edit',
        'handler' => [
            UserController::class,
            'edit',
        ],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/users/edit',
        'handler' => [
            UserController::class,
            'edit',
        ],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/users/delete',
        'handler' => [
            UserController::class,
            'delete',
        ],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/users/2fa/reset',
        'handler' => [
            UserController::class,
            'resetTwoFactor',
        ],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/users/sessions',
        'handler' => [
            UserController::class,
            'sessions',
        ],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/users/sessions/delete',
        'handler' => [
            UserController::class,
            'deleteSession',
        ],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/users/sessions/delete-all',
        'handler' => [
            UserController::class,
            'deleteAllSessions',
        ],
    ],
];