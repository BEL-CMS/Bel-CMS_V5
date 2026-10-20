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

namespace BelCMS\Admin\Email;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

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
    /**
     * Liste des groupes
     */
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

        $config = $this->model->getConfig();

        foreach ($config as $key => $value) {
            $data[$value->name] = $value->value;
        }

        echo $this->view->renderAdmin(
            'Email',
            'index',
            [
                'csrf'    => $this->csrf->field(),
                'config'  => $data,
                'form'    => [],
            ]
        );  
    }

    public function send ()
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

         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->csrf->verify($_POST['csrf_token'] ?? null)) {
                $this->errorPage->show(
                    403,
                    'Requête invalide',
                    'Le jeton de sécurité est invalide ou a expiré.'
                );
                return;
            }
            $data['host']         = trim((string) ($_POST['host'] ?? ''));
            $data['port']         = trim((string) ($_POST['port'] ?? ''));
            $data['SMTPAuth']     = $_POST['SMTPAuth'] == 1 ? 'true' : 'false';
            $data['SMTPAutoTLS']  = $_POST['SMTPAutoTLS'] == 1 ? 'true' : 'false';
            $data['WordWrap']     = is_numeric($_POST['WordWrap']) ? $_POST['WordWrap'] : '65';
            $data['html']         = $_POST['html'] == 1 ? 'true' : 'false';
            $data['sender_email'] = filter_var($_POST['sender_email'], FILTER_VALIDATE_EMAIL) ? $_POST['sender_email'] : '';
            $data['sender_name']  = trim((string) ($_POST['sender_name'] ?? ''));
            $data['charset']      = trim((string) ($_POST['charset'] ?? ''));
            $data['username']     = filter_var($_POST['username'], FILTER_VALIDATE_EMAIL) ? $_POST['username'] : '';
            $data['password']     = trim((string) ($_POST['password'] ?? ''));

            $return = $this->model->save ($data);

            if ($return === true) {
                echo $this->view->renderAdmin(
                    'Email',
                    'index',
                    [
                        'csrf'    => $this->csrf->field(),
                        'config'  => $data,
                        'return'  => true,
                    ]
                );  
            } else {
                echo $this->view->renderAdmin(
                    'Email',
                    'index',
                    [
                        'csrf'    => $this->csrf->field(),
                        'config'  => $data,
                        'return'  => false,
                    ]
                );  
            }
        }
    }
}