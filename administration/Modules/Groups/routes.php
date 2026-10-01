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

use BelCMS\Admin\Groups\Controller as GroupsController;

return [
    [
        'method' => 'GET',
        'path' => '/admin/groups',
        'handler' => [
            GroupsController::class,
            'index',
        ],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/groups/create',
        'handler' => [
            GroupsController::class,
            'create',
        ],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/groups/create',
        'handler' => [
            GroupsController::class,
            'create',
        ],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/groups/edit',
        'handler' => [
            GroupsController::class,
            'edit',
        ],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/groups/edit',
        'handler' => [
            GroupsController::class,
            'edit',
        ],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/groups/delete',
        'handler' => [
            GroupsController::class,
            'delete',
        ],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/groups/members',
        'handler' => [
            GroupsController::class,
            'members',
        ],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/groups/members/add',
        'handler' => [
            GroupsController::class,
            'addMember',
        ],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/groups/members/add',
        'handler' => [
            GroupsController::class,
            'addMember',
        ],
    ],

    [
        'method' => 'POST',
        'path' => '/admin/groups/members/remove',
        'handler' => [
            GroupsController::class,
            'removeMember',
        ],
    ],

];