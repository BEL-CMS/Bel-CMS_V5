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

use BelCMS\Modules\User\Controller;

return [

    'GET /user' => [
        Controller::class,
        'index'
    ],

    'GET /user/login' => [
        Controller::class,
        'login'
    ],

    'POST /user/login' => [
        Controller::class,
        'login'
    ],

    'GET /user/logout' => [
        Controller::class,
        'logout'
    ],

    'GET /user/profile' => [
        Controller::class,
        'profile'
    ],

    'GET /user/edit' => [
        Controller::class,
        'edit'
    ],

    'POST /user/edit' => [
        Controller::class,
        'edit'
    ],

    'GET /user/password' => [
        Controller::class,
        'password'
    ],

    'POST /user/password' => [
        Controller::class,
        'password'
    ],

    'GET /user/security' => [
        Controller::class,
        'security'
    ],

    'GET /user/security/2fa' => [
        Controller::class,
        'twoFactor'
    ],

    'POST /user/security/2fa' => [
        Controller::class,
        'twoFactor'
    ],

    /*
     * Connexion 2FA
     */
    'GET /user/login/2fa' => [
        Controller::class,
        'twoFactorLogin'
    ],

    'POST /user/login/2fa' => [
        Controller::class,
        'twoFactorLogin'
    ],

    'GET /user/security/recovery'  => [
        Controller::class,
        'recovery'
    ],

    'POST /user/security/recovery' => [
        Controller::class,
        'recovery'
    ],
];