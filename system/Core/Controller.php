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

namespace BelCMS\Modules\Profils;

use BelCMS\Core\User;
use BelCMS\Core\View;
use BelCMS\Core\Language;
use BelCMS\Core\Assets;

if (!defined('CHECK_INDEX')):
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
    exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on the server.</p></body></html>');
endif;

final class Controller
{
    private Model $model;
    private View $view;
    private Language $language;
    private Assets $assets;
    private User $user;

    public function __construct(
        Model $model,
        View $view,
        Language $language,
        Assets $assets,
        User $user
    ) {
        $this->model = $model;
        $this->view = $view;
        $this->language = $language;
        $this->assets = $assets;
        $this->user = $user;
    }

    /**
     * Affichage du profil.
     */
    public function index(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        $this->assets->moduleCss('Profils');
        $this->assets->moduleJs('Profils');

        $hashKey = $this->user->hashKey();

        if ($hashKey === null || $hashKey === '') {
            header('Location: /user/login');
            exit;
        }

        $profile = $this->model->getByHashKey($hashKey);

        echo $this->view->render(
            'Profils',
            'index',
            [
                'profile'  => $profile,
                'profils'  => $profile,
                'language' => $this->language,
            ]
        );
    }

    /**
     * Modification du profil.
     */
    public function edit(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        $this->assets->moduleCss('Profils');
        $this->assets->moduleCssFile('Profils', 'edit.css');

        $this->assets->moduleJs('Profils');
        $this->assets->moduleJsFile('Profils', 'edit.js');

        $hashKey = $this->user->hashKey();

        if ($hashKey === null || $hashKey === '') {
            header('Location: /profile');
            exit;
        }

        $profile = $this->model->getByHashKey($hashKey);

        $errors = [];
        $success = null;

        echo $this->view->render(
            'Profils',
            'edit',
            [
                'profile'  => $profile,
                'errors'   => $errors,
                'success'  => $success,
                'language' => $this->language,
            ]
        );
    }
}
