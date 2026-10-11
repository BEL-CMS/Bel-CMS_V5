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

use BelCMS\Admin\Banishment\Controller as BanishmentController;

if (!defined('CHECK_INDEX')) { http_response_code(403); exit('Accès interdit.'); }

return [
 ['method'=>'GET','path'=>'/admin/banishment','handler'=>[BanishmentController::class,'index']],
 ['method'=>'GET','path'=>'/admin/banishment/create','handler'=>[BanishmentController::class,'create']],
 ['method'=>'POST','path'=>'/admin/banishment/create','handler'=>[BanishmentController::class,'store']],
 ['method'=>'POST','path'=>'/admin/banishment/revoke','handler'=>[BanishmentController::class,'revoke']],
 ['method'=>'GET','path'=>'/admin/banishment/logs','handler'=>[BanishmentController::class,'logs']],
];
