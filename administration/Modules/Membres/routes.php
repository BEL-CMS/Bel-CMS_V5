<?php
declare(strict_types=1);
use BelCMS\Admin\Membres\Controller as MembresController;
return [
    [
        'method' => 'GET',
        'path' => '/admin/members/edit',
        'handler' => [MembresController::class, 'edit'],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/members/edit',
        'handler' => [MembresController::class, 'edit'],
    ],
    [
        'method' => 'GET',
        'path' => '/admin/members',
        'handler' => [MembresController::class, 'index'],
    ],
];
