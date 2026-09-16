<?php

declare(strict_types=1);

use BelCMS\Admin\Controller;

return [

    'GET /admin' => [
        Controller::class,
        'index'
    ],

    'GET /admin/users' => [
        Controller::class,
        'users'
    ],

    'GET /admin/users/edit' => [
        Controller::class,
        'editUser'
    ],

    'POST /admin/users/edit' => [
        Controller::class,
        'editUser'
    ],

    'POST /admin/users/2fa/reset' => [
        Controller::class,
        'resetTwoFactor'
    ],

    'GET /admin/users/sessions' => [
        Controller::class,
        'userSessions'
    ],

    'POST /admin/users/sessions/delete' => [
        Controller::class,
        'deleteUserSession'
    ],

];