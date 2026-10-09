<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 * @author as Stive - stive@determe.be
*/

declare(strict_types=1);

return [
    'name'    => 'news',
    'version' => '1.0.0',
    'author'  => 'Bel-CMS',
    'admin' => [
        'label' => 'Actualités',
        'icon'  => 'fa-newspaper',
        'route' => '/admin/news',
        'order' => 10,
        'menu' => [],
    ],
    'assets' => [
        'css' => true,
        'js' => true,
    ],
];
