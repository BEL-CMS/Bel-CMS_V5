<?php


declare(strict_types=1);

return [
    'name'    => 'User',
    'version' => '1.0.0',
    'author'  => 'Bel-CMS',
    'admin' => [
        'label' => 'Utilisateurs',
        'icon' => 'fa-users',
        'order' => 10,
        'menu' => [
            [
                'label' => 'Membres',
                'route' => '/admin/users',
                'icon'  => 'fa-users',
                'order' => 10,
            ],
            [
                'label' => 'À valider',
                'route' => '/admin/users/valid',
                'icon'  => 'fa-solid fa-person-circle-check',
                'order' => 20,
            ],
            [
                'label' => 'Bannissement',
                'route' => '/admin/users/banishment',
                'icon'  => 'fa-ban',
                'order' => 30,
            ],
        ],
    ],
    'assets' => [
        'css' => true,
        'js' => true,
    ],
];