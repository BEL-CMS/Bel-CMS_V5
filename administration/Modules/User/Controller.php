<?php

declare(strict_types=1);

namespace BelCMS\Admin\User;

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


    /**
     * Liste des utilisateurs
     */
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

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $search = trim(
            (string) ($_GET['search'] ?? '')
        );

        $perPage = 25;

        $users = $this->model->getUsers(
            $page,
            $perPage,
            $search
        );

        $total = $this->model->countUsers(
            $search
        );

        $pages = (int) ceil(
            $total / $perPage
        );

        $this->assets->css(
            '/administration/Modules/User/css/user.css'
        );

        echo $this->view->renderAdmin(
            'User',
            'index',
            [
                'user'    => $this->user,
                'users'   => $users,
                'page'    => $page,
                'perPage' => $perPage,
                'total'   => $total,
                'pages'   => $pages,
                'search'  => $search,
            ]
        );
    }
/**
 * Édition d'un utilisateur.
 */
/**
 * Édition d'un utilisateur.
 */
public function edit(): void
{
    if (!$this->user->isLogged()) {
        header('Location: /user/login');
        exit;
    }

    if (!$this->user->isAdmin()) {
        http_response_code(403);
        exit('Accès interdit.');
    }

    $hashKey = trim(
        (string) ($_GET['hash_key'] ?? $_POST['hash_key'] ?? '')
    );

    if ($hashKey === '') {
        http_response_code(400);
        exit('Utilisateur invalide.');
    }

    $targetUser = $this->model->getUserByHashKey($hashKey);

    if ($targetUser === null) {
        http_response_code(404);
        exit('Utilisateur introuvable.');
    }

    /*
     * Traitement du formulaire
     */
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            exit('Jeton CSRF invalide.');
        }

        $username = trim(
            (string) ($_POST['username'] ?? '')
        );

        $email = trim(
            (string) ($_POST['email'] ?? '')
        );

        $valid = isset($_POST['valid']) ? 1 : 0;

        /*
         * Validation
         */
        if ($username === '') {
            http_response_code(400);
            exit('Le nom d\'utilisateur est obligatoire.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            exit('Adresse email invalide.');
        }

        /*
         * Données à mettre à jour
         */
        $data = [
            'username' => $username,
            'email'    => $email,
            'valid'    => $valid,
        ];

        /*
         * Mise à jour
         */
        $this->model->updateUser(
            $hashKey,
            $data
        );

        /*
         * Retour sur la page d'édition
         */
        header(
            'Location: /admin/users/edit?hash_key='
            . urlencode($hashKey)
            . '&saved=1'
        );

        exit;
    }

    /*
     * CSS du module
     */
    $this->assets->css(
        '/administration/Modules/User/css/user.css'
    );

    /*
     * Affichage
     */
    echo $this->view->renderAdmin(
        'User',
        'edit',
        [
            'user'       => $this->user,
            'targetUser' => $targetUser,
            'saved'      => isset($_GET['saved']),
        ]
    );
}
}