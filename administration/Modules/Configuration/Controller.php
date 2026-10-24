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

namespace BelCMS\Admin\Configuration;

use BelCMS\Core\Assets;
use BelCMS\Core\Csrf;
use BelCMS\Core\Errors\ErrorPage;
use BelCMS\Core\View;
use BelCMS\Core\User;

final class Controller
{
    public function __construct(
        private View $view,
        private Assets $assets,
        private Model $model,
        private Csrf $csrf,
        private ErrorPage $errorPage,
        private User $user
    ) {
    }

    public function index(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        if (!$this->user->isAdmin()) {
            $this->errorPage->show(
                403,
                'Administration',
                'Accès interdit'
            );
        }

        $this->assets->css('configuration.css');

        $data = $this->model->getConfig();

        echo $this->view->renderAdmin(
            'configuration',
            'index',
            [
                'csrf'   => $this->csrf->field(),
                'config' => $data
            ]
        );
    }

    public function save ()
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }
        if (!$this->user->isAdmin()) {
            $this->errorPage->show(
                403,
                'Administration',
                'Accès interdit'
            );
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_verify($_POST['csrf_token'] ?? null)) {
                $this->errorPage->show(
                    400,
                    'Administration',
                    'Jeton CSRF invalide.'
                );
            }

            $data['NAME_WEB']    = trim((string) ($_POST['title']) ?? '');
            $data['DESCRIPTION'] = trim((string) ($_POST['description']) ?? '');
            $data['KEYWORD']     = trim((string) ($_POST['key']) ?? '');
            $data['CHATER']      = trim((string) ($_POST['charte']) ?? '');
            $data['EMAIL']       = trim((string) ($_POST['mail']) ?? '');

            if (!filter_var($data['EMAIL'], FILTER_VALIDATE_EMAIL)) {
                $this->errorPage->show(
                    400,
                    'Administration',
                    'Adresse email invalide.'
                );
            }

            $save   = $this->model->save($data);
            if ($save === true) {

                $config = $this->model->getConfig();
                
                echo $this->view->renderAdmin(
                    'configuration',
                    'index',
                    [
                        'csrf'   => $this->csrf->field(),
                        'save'   => 'Vos données ont été correctement enregistrées.',
                        'config' => $config
                    ]
                );  
            } else {
                echo $this->view->renderAdmin(
                    'configuration',
                    'save',
                    [
                        'csrf'   => $this->csrf->field(),
                        'errors' => 'Une erreur est survenue lors de l\'enregistrement en base de données.'
                    ]
                );                
            }



        }
    }
}