<?php

declare(strict_types=1);

namespace BelCMS\Admin\Dashboard;

use BelCMS\Core\Assets;
use BelCMS\Core\User;
use BelCMS\Core\View;

final class Controller
{
    public function __construct(
        private User $user,
        private View $view,
        private Assets $assets,
        private Model $model
    ) {
    }

    public function index(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        if (!$this->user->isAdmin()) {
            http_response_code(403);
            exit('Accès interdit.');
        }

        $statistics = $this->model->getStatistics();

        $this->assets->css(
            '/administration/Modules/Dashboard/css/dashboard.css'
        );

        echo $this->view->renderAdmin(
            'Dashboard',
            'index',
            [
                'user'        => $this->user,
                'statistics'  => $statistics,
            ]
        );
    }
}