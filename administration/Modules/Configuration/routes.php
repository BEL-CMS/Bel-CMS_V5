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

use BelCMS\Admin\Configuration\Controller as ConfigurationController;

if (!defined('CHECK_INDEX')) { http_response_code(403); exit('Accès interdit.');}

return [
    [
        'method' => 'GET',
        'path' => '/admin/settings',
        'handler' => [
            ConfigurationController::class,
            'index',
        ],
    ],
    [
        'method' => 'POST',
        'path' => '/admin/settings/save',
        'handler' => [
            ConfigurationController::class,
            'save',
        ],
    ],
];