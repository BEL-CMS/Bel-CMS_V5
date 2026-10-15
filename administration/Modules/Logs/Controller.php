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

namespace BelCMS\Admin\Logs;

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

        $this->assets->css(
            '/administration/Modules/Logs/css/logss.css'
        );
        $this->assets->css(
            '/assets/plugins/datatables/datatables.min.css'
        );
        $this->assets->css(
            '/assets/plugins/datatables/belcms-datatables.css'
        );
        $this->assets->css(
            '/assets/plugins/tooltip/scale.css'
        );
        $this->assets->js(
            '/assets/plugins/datatables/datatables.min.js'
        );
        $this->assets->js(
            '/assets/plugins/tooltip/popper.min.js'
        );
        $this->assets->js(
            '/assets/plugins/tooltip/tippy-bundle.umd.min.js'
        );
        $this->assets->js(
            '/assets/plugins/tooltip/tooltip.js'
        );
 
        $logs = $this->model->getAllLogs();

        echo $this->view->renderAdmin(
            'Logs',
            'index',
            [
                'logs' => $logs
            ]
        );
    }
}