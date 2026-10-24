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

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

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