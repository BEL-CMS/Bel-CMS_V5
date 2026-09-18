<?php

declare(strict_types=1);

use BelCMS\Admin\Dashboard\Controller as DashboardController;
use BelCMS\Admin\User\Controller as UserController;
use BelCMS\Admin\Membres\Controller as MembresController;

return [

    'GET /admin' => [
        DashboardController::class,
        'index'
    ],

    'GET /admin/users' => [
        UserController::class,
        'index'
    ],

    'GET /admin/users/edit' => [
        UserController::class,
        'edit'
    ],

    'POST /admin/users/edit' => [
        UserController::class,
        'edit'
    ],

    'POST /admin/users/delete' => [
        UserController::class,
        'delete'
    ],

    'POST /admin/users/2fa/reset' => [
        UserController::class,
        'resetTwoFactor'
    ],

    'GET /admin/users/sessions' => [
        UserController::class,
        'sessions'
    ],

    'POST /admin/users/sessions/delete' => [
        UserController::class,
        'deleteSession'
    ],

    'POST /admin/users/sessions/delete-all' => [
        UserController::class,
        'deleteAllSessions'
    ],
    
];
