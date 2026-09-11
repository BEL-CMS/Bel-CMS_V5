<?php
/**
 * Bel-CMS User module routes
 */

declare(strict_types=1);

use BelCMS\Modules\User\Controller;

return [
    'GET /user' => [Controller::class, 'index'],
    'GET /user/login' => [Controller::class, 'login'],
    'POST /user/login' => [Controller::class, 'login'],
    'GET /user/logout' => [Controller::class, 'logout'],
    'GET /user/profile' => [Controller::class, 'profile'],
    'GET /user/edit' => [Controller::class, 'edit'],
    'POST /user/edit' => [Controller::class, 'edit'],
    'GET /user/password' => [Controller::class, 'password'],
    'POST /user/password' => [Controller::class, 'password'],
    'GET /user/security' => [Controller::class, 'security'],
    'GET /user/security/2fa' => [Controller::class, 'twoFactor'],
    'POST /user/security/2fa' => [Controller::class, 'twoFactor'],
    'GET /user/login/2fa' => [Controller::class, 'twoFactorLogin'],
    'POST /user/login/2fa' => [Controller::class, 'twoFactorLogin'],
    'GET /user/security/recovery' => [Controller::class, 'recovery'],
    'POST /user/security/recovery' => [Controller::class, 'recovery'],
    'GET /user/security/sessions' => [Controller::class, 'sessions'],
    'POST /user/security/sessions' => [Controller::class, 'sessions'],
    'GET /user/register' => [Controller::class, 'register'],
    'POST /user/register' => [Controller::class, 'register'],
    'GET /user/verify' => [Controller::class, 'verify'],
];
