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

namespace BelCMS\Admin;

use BelCMS\Core\Assets;
use BelCMS\Core\BDD;
use BelCMS\Core\ModuleManager;
use BelCMS\Core\User;
use BelCMS\Core\UserSession;
use BelCMS\Core\View;

final class Controller
{
    private User $user;
    private View $view;
    private BDD $db;
    private ModuleManager $modules;
    private Assets $assets;
    private UserSession $userSession;

    public function __construct(
        User $user,
        View $view,
        BDD $db,
        ModuleManager $modules,
        Assets $assets,
        UserSession $userSession
    ) {
        $this->user        = $user;
        $this->view        = $view;
        $this->db          = $db;
        $this->modules     = $modules;
        $this->assets      = $assets;
        $this->userSession = $userSession;
    }
    /**
     * Tableau de bord
     */
    public function index(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        if (!$this->user->isAdmin()) {
            http_response_code(403);

            echo $this->view->renderAdmin(
                'index',
                [
                    'accessDenied' => true,
                    'user' => $this->user->data(),
                ]
            );

            return;
        }

        $this->db->table('TABLE_USERS');
        $totalUsers = $this->db->count();

        $this->db->table('TABLE_GROUPS');
        $totalGroups = $this->db->count();

        $totalModules = count($this->modules->all());

        $twoFactorEnabled = $this->user->isTwoFactorEnabled();

        echo $this->view->renderAdmin(
            'index',
            [
                'accessDenied'     => false,
                'user'             => $this->user->data(),
                'totalUsers'       => $totalUsers,
                'totalGroups'      => $totalGroups,
                'totalModules'     => $totalModules,
                'twoFactorEnabled' => $twoFactorEnabled,
            ]
        );
    }

    /**
     * Liste des utilisateurs
     */
    public function users(): void
    {
        $this->assets->css('/administration/css/users.css');
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        if (!$this->user->isAdmin()) {
            http_response_code(403);

            echo $this->view->renderAdmin(
                'index',
                [
                    'accessDenied' => true,
                    'user' => $this->user->data(),
                ]
            );

            return;
        }

        /*
         * Recherche
         */
        $search = trim(
            (string)($_GET['search'] ?? '')
        );

        /*
         * Page
         */
        $page = max(
            1,
            (int)($_GET['page'] ?? 1)
        );

        /*
         * Nombre d'utilisateurs par page
         */
        $perPage = 25;

        /*
         * Compteur total
         */
        $this->db->table('TABLE_USERS');

        if ($search !== '') {
            $this->db->whereLike([
                'name'  => 'username',
                'value' => $search,
            ]);

            $totalUsers = $this->db->count();

            /*
             * Nouvelle requête pour récupérer les données
             */
            $this->db->table('TABLE_USERS');

            $this->db->whereLike([
                'name'  => 'username',
                'value' => $search,
            ]);
        } else {
            $totalUsers = $this->db->count();

            $this->db->table('TABLE_USERS');
        }

        /*
         * Pagination
         */
        $totalPages = max(
            1,
            (int)ceil($totalUsers / $perPage)
        );

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $perPage;

        /*
         * Liste
         */
        $this->db->fields([
            'id',
            'hash_key',
            'username',
            'email',
            'valid',
            'admin',
            'is_admin',
            'ip'
        ]);

        if ($search !== '') {
            $this->db->whereLike([
                'name'  => 'username',
                'value' => $search,
            ]);
        }

        $this->db->orderby([
            [
                'name' => 'id',
                'type' => 'DESC',
            ]
        ]);

        $this->db->limit([
            $offset,
            $perPage
        ], true);

        $users = $this->db->queryAll();

        echo $this->view->renderAdmin(
            'users',
            [
                'user'         => $this->user->data(),
                'users'        => $users,
                'search'       => $search,
                'page'         => $page,
                'perPage'      => $perPage,
                'totalUsers'   => $totalUsers,
                'totalPages'   => $totalPages,
            ]
        );
    }
    /**
     * Modification d'un utilisateur
     */
    public function editUser(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        if (!$this->user->isAdmin()) {
            http_response_code(403);

            echo $this->view->renderAdmin(
                'index',
                [
                    'accessDenied' => true,
                    'user' => $this->user->data(),
                ]
            );

            return;
        }

        /*
        * Identifiant utilisateur
        */
        $hashKey = trim(
            (string)($_GET['hash_key'] ?? $_POST['hash_key'] ?? '')
        );

        if ($hashKey === '') {
            header('Location: /admin/users');
            exit;
        }

        /*
        * Récupération
        */
        $this->db->table('TABLE_USERS');

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);

        $this->db->queryOne();

        $editUser = $this->db->data;

        if (!is_object($editUser)) {

            http_response_code(404);

            echo $this->view->renderAdmin(
                'users',
                [
                    'user' => $this->user->data(),
                    'editUser' => null,
                    'errors' => [
                        'Utilisateur introuvable.'
                    ],
                    'success' => null,
                ]
            );

            return;
        }

        $errors = [];
        $success = null;

        /*
        * Traitement du formulaire
        */
        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
        ) {

            /*
            * CSRF
            */
            if (!csrf_verify($_POST['csrf_token'] ?? null)) {

                $errors[] =
                    'Votre session de sécurité a expiré. Veuillez réessayer.';

            } else {

                $username = trim(
                    (string)($_POST['username'] ?? '')
                );

                $email = trim(
                    (string)($_POST['email'] ?? '')
                );

                $valid = isset($_POST['valid']) ? 1 : 0;

                $admin = isset($_POST['admin']) ? 1 : 0;


                /*
                * Validation username
                */
                if ($username === '') {

                    $errors[] =
                        'Le nom d’utilisateur est obligatoire.';

                } elseif (
                    mb_strlen($username) < 3
                    || mb_strlen($username) > 100
                ) {

                    $errors[] =
                        'Le nom d’utilisateur doit contenir entre 3 et 100 caractères.';
                }


                /*
                * Validation email
                */
                if (
                    $email === ''
                    || !filter_var(
                        $email,
                        FILTER_VALIDATE_EMAIL
                    )
                ) {

                    $errors[] =
                        'Veuillez fournir une adresse e-mail valide.';
                }

$newPassword = trim((string)($_POST['new_password'] ?? ''));
$newPasswordConfirm = trim((string)($_POST['new_password_confirm'] ?? ''));

if ($newPassword !== '' || $newPasswordConfirm !== '') {

    if ($newPassword === '' || $newPasswordConfirm === '') {
        $errors[] = 'Veuillez remplir les deux champs du mot de passe.';
    } elseif (strlen($newPassword) < 8) {
        $errors[] = 'Le nouveau mot de passe doit contenir au minimum 8 caractères.';
    } elseif ($newPassword !== $newPasswordConfirm) {
        $errors[] = 'Les deux mots de passe ne correspondent pas.';
    }
}


                /*
                * Vérification username existant
                */
                if (!$errors) {

                    $this->db->table('TABLE_USERS');

                    $this->db->where([
                        [
                            'name'  => 'username',
                            'value' => $username,
                        ],
                        [
                            'name'  => 'id',
                            'value' => (int)$editUser->id,
                            'op'    => '!=',
                        ],
                    ]);

                    $this->db->queryOne();

                    if (is_object($this->db->data)) {

                        $errors[] =
                            'Ce nom d’utilisateur est déjà utilisé.';
                    }
                }


                /*
                * Vérification email existant
                */
                if (!$errors) {

                    $this->db->table('TABLE_USERS');

                    $this->db->where([
                        [
                            'name'  => 'email',
                            'value' => $email,
                        ],
                        [
                            'name'  => 'id',
                            'value' => (int)$editUser->id,
                            'op'    => '!=',
                        ],
                    ]);

                    $this->db->queryOne();

                    if (is_object($this->db->data)) {

                        $errors[] =
                            'Cette adresse e-mail est déjà utilisée.';
                    }
                }


                /*
                * Mise à jour
                */
                if (!$errors) {

                    $this->db->table('TABLE_USERS');

                    $this->db->where([
                        'name'  => 'hash_key',
                        'value' => $hashKey,
                    ]);

                    $updated = $this->db->update([
                        'username' => $username,
                        'email'    => $email,
                        'valid'    => $valid,
                        'admin'    => $admin,
                    ]);

                    if ($updated) {

                        $success =
                            'Les informations de l’utilisateur ont été mises à jour.';

                        /*
                        * Relecture
                        */
                        $this->db->table('TABLE_USERS');

                        $this->db->where([
                            'name'  => 'hash_key',
                            'value' => $hashKey,
                        ]);

                        $this->db->queryOne();

                        $editUser = $this->db->data;

                    } else {

                        $errors[] =
                            'Impossible de mettre à jour cet utilisateur.';
                    }
                }
            }
        }


        /*
        * CSS
        */
        $this->assets->css(
            '/administration/css/users.css'
        );


        echo $this->view->renderAdmin(
            'user-edit',
            [
                'user'      => $this->user->data(),
                'editUser'  => $editUser,
                'errors'    => $errors,
                'success'   => $success,
            ]
        );
    }
    /**
     * Suppression d'un utilisateur
     */
    public function deleteUser(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        if (!$this->user->isAdmin()) {
            http_response_code(403);
            exit('Accès interdit.');
        }

        /*
        * Vérification CSRF
        */
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit('Session de sécurité expirée.');
        }

        /*
        * Récupération du hash_key
        */
        $hashKey = trim(
            (string)($_POST['hash_key'] ?? '')
        );

        if ($hashKey === '') {
            header('Location: /admin/users');
            exit;
        }

        /*
        * Sécurité :
        * empêcher la suppression du compte actuellement connecté.
        */
        $currentHashKey = $this->user->hashKey();

        if (
            $currentHashKey !== null
            && hash_equals($currentHashKey, $hashKey)
        ) {
            header('Location: /admin/users?error=self-delete');
            exit;
        }

        /*
        * Vérifie que l'utilisateur existe
        */
        $this->db->table('TABLE_USERS');

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);

        $this->db->queryOne();

        $targetUser = $this->db->data;

        if (!is_object($targetUser)) {
            header('Location: /admin/users?error=not-found');
            exit;
        }

        /*
        * Suppression
        */
        $this->db->table('TABLE_USERS');

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
        ]);

        $deleted = $this->db->delete();

        if (!$deleted) {
            header('Location: /admin/users?error=delete');
            exit;
        }

        /*
        * Retour à la liste
        */
        header('Location: /admin/users?success=deleted');
        exit;
    }
    /**
     * Réinitialisation du 2FA d'un utilisateur.
     */
    public function resetTwoFactor(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        if (!$this->user->isAdmin()) {
            http_response_code(403);

            echo $this->view->renderAdmin(
                'index',
                [
                    'accessDenied' => true,
                    'user'         => $this->user->data(),
                ]
            );

            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/users');
            exit;
        }

        /*
        * Vérification CSRF.
        */
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            header('Location: /admin/users?error=csrf');
            exit;
        }

        $hashKey = trim(
            (string)($_POST['hash_key'] ?? '')
        );

        if ($hashKey === '') {
            header('Location: /admin/users?error=invalid_user');
            exit;
        }

        /*
        * Vérifie que l'utilisateur existe.
        */
        $this->db->table('TABLE_USERS');

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
            'op'    => '=',
        ]);

        $targetUser = $this->db->queryOne();

        if (!is_object($targetUser)) {
            header('Location: /admin/users?error=user_not_found');
            exit;
        }

        /*
        * Désactivation du 2FA.
        */
        $this->db->table('TABLE_USERS');

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
            'op'    => '=',
        ]);

        $updated = $this->db->update([
            'two_factor_enabled' => 0,
            'two_factor_secret'  => null,
        ]);

        /*
        * Suppression des anciens codes de récupération.
        */
        $this->db->table('TABLE_RECOVERY');

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
            'op'    => '=',
        ]);

        $this->db->delete();

        if (!$updated) {
            header('Location: /admin/users/edit?hash_key='
                . urlencode($hashKey)
                . '&error=2fa_reset'
            );
            exit;
        }

        header(
            'Location: /admin/users/edit?hash_key='
            . urlencode($hashKey)
            . '&success=2fa_reset'
        );

        exit;
    }
    /**
     * Affiche les sessions actives d'un utilisateur.
     */
    public function userSessions(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        if (!$this->user->isAdmin()) {
            http_response_code(403);

            echo $this->view->renderAdmin(
                'index',
                [
                    'accessDenied' => true,
                    'user'         => $this->user->data(),
                ]
            );

            return;
        }

        $hashKey = trim(
            (string)($_GET['hash_key'] ?? '')
        );

        if ($hashKey === '') {
            header('Location: /admin/users?error=invalid_user');
            exit;
        }

        /*
        * Récupération de l'utilisateur.
        */
        $this->db->table(TABLE_USERS);

        $this->db->where([
            'name'  => 'hash_key',
            'value' => $hashKey,
            'op'    => '=',
        ]);

        $targetUser = $this->db->queryOne();

        if (!is_object($targetUser)) {
            header('Location: /admin/users?error=user_not_found');
            exit;
        }

        /*
        * Récupération des sessions.
        */
        $sessions = $this->userSession->getByHashKey($hashKey);

        /*
        * Charge la feuille CSS du module utilisateurs.
        */
        $this->assets->css(
            '/administration/css/users.css'
        );

        echo $this->view->renderAdmin(
            'user-sessions',
            [
                'user'     => $targetUser,
                'sessions' => $sessions,
            ]
        );
    }
    /**
     * Supprime une session précise d'un utilisateur.
     */
    public function deleteUserSession(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        if (!$this->user->isAdmin()) {
            http_response_code(403);

            echo $this->view->renderAdmin(
                'index',
                [
                    'accessDenied' => true,
                    'user'         => $this->user->data(),
                ]
            );

            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/users');
            exit;
        }

        /*
        * CSRF
        */
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            header('Location: /admin/users?error=csrf');
            exit;
        }

        $hashKey = trim(
            (string)($_POST['hash_key'] ?? '')
        );

        $sessionId = trim(
            (string)($_POST['session_id'] ?? '')
        );

        if ($hashKey === '' || $sessionId === '') {
            header('Location: /admin/users');
            exit;
        }

        /*
        * Vérifie que la session appartient bien
        * à l'utilisateur demandé.
        */
        $sessions = $this->userSession->getByHashKey($hashKey);

        $sessionExists = false;

        foreach ($sessions as $session) {

            if (
                isset($session->session_id)
                && hash_equals(
                    (string)$session->session_id,
                    $sessionId
                )
            ) {
                $sessionExists = true;
                break;
            }
        }

        if (!$sessionExists) {
            header(
                'Location: /admin/users/sessions?hash_key='
                . urlencode($hashKey)
                . '&error=session_not_found'
            );
            exit;
        }

        /*
        * Supprime la session.
        */
        $deleted = $this->userSession->delete(
            $sessionId
        );

        header(
            'Location: /admin/users/sessions?hash_key='
            . urlencode($hashKey)
            . ($deleted ? '&success=session_deleted' : '&error=session_delete')
        );

        exit;
    }

}