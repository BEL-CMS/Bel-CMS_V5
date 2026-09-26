<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */
declare(strict_types=1);

use BelCMS\Admin\Dashboard\Controller as DashboardController;
use BelCMS\Admin\User\Controller as UserController;
use BelCMS\Admin\Membres\Controller as MembresController;
use BelCMS\Admin\Groups\Controller as GroupsController;
use BelCMS\Admin\News\Controller as NewsController;
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
    'GET /admin/groups' => [
        GroupsController::class, 
        'index'
    ],
    'GET /admin/groups/create' => [
        GroupsController::class,
        'create'
    ],
    'POST /admin/groups/create' => [
        GroupsController::class,
        'create'
    ],
    'GET /admin/groups/edit' => [
        GroupsController::class,
        'edit'
    ],
    'POST /admin/groups/edit' => [
        GroupsController::class,
        'edit'
    ],
    'POST /admin/groups/delete' => [
        GroupsController::class, 
        'delete'
    ],
    'GET /admin/groups/members' => [
        GroupsController::class,
        'members'
    ],
    'GET /admin/groups/members/add' => 
        [GroupsController::class,
        'addMember'
    ],
    'POST /admin/groups/members/add' => [
        GroupsController::class,
        'addMember'
    ],
    'POST /admin/groups/members/remove' => [
        GroupsController::class,
        'removeMember'
    ],
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
