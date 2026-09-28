<?php

declare(strict_types=1);

use BelCMS\Admin\Groups\Controller as GroupsController;

return [

    'GET /admin/groups' => [
        GroupsController::class,
        'index',
    ],

    'GET /admin/groups/create' => [
        GroupsController::class,
        'create',
    ],

    'POST /admin/groups/create' => [
        GroupsController::class,
        'create',
    ],

    'GET /admin/groups/edit' => [
        GroupsController::class,
        'edit',
    ],

    'POST /admin/groups/edit' => [
        GroupsController::class,
        'edit',
    ],

    'POST /admin/groups/delete' => [
        GroupsController::class,
        'delete',
    ],

    'GET /admin/groups/members' => [
        GroupsController::class,
        'members',
    ],

    'GET /admin/groups/members/add' => [
        GroupsController::class,
        'addMember',
    ],

    'POST /admin/groups/members/add' => [
        GroupsController::class,
        'addMember',
    ],

    'POST /admin/groups/members/remove' => [
        GroupsController::class,
        'removeMember',
    ],

];