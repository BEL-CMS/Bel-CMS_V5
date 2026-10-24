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
    'name'    => 'Configuration',
    'version' => '1.0.0',
    'author'  => 'Bel-CMS',
    'admin' => [
        'label'    => 'Configuration',
        'icon'     => 'fa-gear',
        'route'    => '/admin/settings',
        'category' => 'Paramètres',
        'order' => 10,
    ],
    'assets' => [
        'css' => true,
        'js' => false,
    ],
];