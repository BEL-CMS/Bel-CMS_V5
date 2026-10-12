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

return [
    'name'    => 'users',
    'version' => '1.0.0',
    'author'  => 'Bel-CMS',
    'admin' => [
        'label'    => 'Utilisateurs',
        'icon'     => 'fa-users',
        'route'    => '/admin/users',
        'category' => 'Utilisateurs',
        'order' => 5,
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
            ]
        ],
    ],
    'assets' => [
        'css' => true,
        'js' => true,
    ],
];
