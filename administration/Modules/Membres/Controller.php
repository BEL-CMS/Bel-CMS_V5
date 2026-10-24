<?php
/** Bel-CMS V5 — Administration des membres | Apache-2.0 */
declare(strict_types=1);
namespace BelCMS\Admin\Membres;
use BelCMS\Core\Assets;
use BelCMS\Core\User;
use BelCMS\Core\View;
use BelCMS\Core\Errors\ErrorPage;
final class Controller
{
    public function __construct(
        private User $user,
        private View $view,
        private Assets $assets,
        private Model $model,
        private ErrorPage $errorPage
    ) {}

    public function index(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }
        if (!$this->user->isAdmin()) {
            $this->errorPage->show(403, 'Administration', 'Accès interdit.');
            return;
        }
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $search = mb_substr(trim((string) ($_GET['search'] ?? '')), 0, 100);
        $perPage = 25;
        $total = $this->model->countMembers($search);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);
        $this->assets->css('/administration/Modules/Membres/css/membres.css');
        echo $this->view->renderAdmin('Membres', 'index', [
            'members' => $this->model->getMembers($page, $perPage, $search),
            'page' => $page, 'pages' => $pages, 'perPage' => $perPage,
            'total' => $total, 'search' => $search,
        ]);
    }
    public function edit(): void
    {
        if (!$this->user->isLogged()) { header('Location: /user/login'); exit; }
        if (!$this->user->isAdmin()) { $this->errorPage->show(403, 'Administration', 'Accès interdit.'); return; }

        $hashKey = trim((string) ($_GET['hash_key'] ?? $_POST['hash_key'] ?? ''));
        if ($hashKey === '') { $this->errorPage->show(400, 'Administration', 'Membre invalide.'); return; }
        $record = $this->model->getMemberForEdit($hashKey);
        if ($record === null) { $this->errorPage->show(404, 'Administration', 'Membre introuvable.'); return; }

        $errors = []; $success = null;
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_verify($_POST['csrf_token'] ?? null)) {
                $errors[] = 'La session de sécurité a expiré. Recharge la page et réessaie.';
            } else {
                $username = trim((string) ($_POST['username'] ?? ''));
                $email = trim((string) ($_POST['email'] ?? ''));
                $publicMail = trim((string) ($_POST['public_mail'] ?? ''));
                $website = trim((string) ($_POST['websites'] ?? ''));
                if ($username === '' || mb_strlen($username) < 3 || mb_strlen($username) > 100) $errors[] = 'Le pseudo doit contenir entre 3 et 100 caractères.';
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'L’adresse e-mail du compte est invalide.';
                if ($publicMail !== '' && !filter_var($publicMail, FILTER_VALIDATE_EMAIL)) $errors[] = 'L’adresse e-mail publique est invalide.';
                if ($website !== '' && (!filter_var($website, FILTER_VALIDATE_URL) || !preg_match('~^https?://~i', $website))) $errors[] = 'Le site web doit être une URL HTTP ou HTTPS valide.';

                if ($errors === []) {
                    $accountData = ['username' => $username, 'email' => $email, 'valid' => isset($_POST['valid']) ? 1 : 0];
                    $profileData = [
                        'last_name' => trim((string) ($_POST['last_name'] ?? '')),
                        'gender' => trim((string) ($_POST['gender'] ?? '')),
                        'public_mail' => $publicMail, 'websites' => $website,
                        'infos_text' => trim((string) ($_POST['infos_text'] ?? '')),
                        'country' => trim((string) ($_POST['country'] ?? '')),
                        'profils' => trim((string) ($_POST['profils'] ?? '')),
                        'avatar' => trim((string) ($_POST['avatar'] ?? '')),
                        'cover_avatar' => trim((string) ($_POST['cover_avatar'] ?? '')),
                        'gravatar' => trim((string) ($_POST['gravatar'] ?? '')),
                    ];
                    if ($this->model->updateMember($hashKey, $accountData, $profileData)) {
                        header('Location: /admin/members/edit?hash_key=' . rawurlencode($hashKey) . '&saved=1'); exit;
                    }
                    $errors[] = 'La modification a échoué. Vérifie les URL et les champs, puis réessaie.';
                }
            }
            // Keep submitted safe fields visible if validation fails.
            $record['account']->username = $username ?? $record['account']->username;
            $record['account']->email = $email ?? $record['account']->email;
            foreach (['last_name','gender','public_mail','websites','infos_text','country','profils','avatar','cover_avatar','gravatar'] as $field) {
                if (isset($profileData) && array_key_exists($field, $profileData) && $record['profile'] !== null) $record['profile']->$field = $profileData[$field];
            }
        }
        if (isset($_GET['saved']) && $_GET['saved'] === '1') $success = 'Les modifications ont été enregistrées.';
        $this->assets->css('/administration/Modules/Membres/css/membres.css');
        echo $this->view->renderAdmin('Membres', 'edit', [
            'account' => $record['account'], 'profile' => $record['profile'],
            'hashKey' => $hashKey, 'errors' => $errors, 'success' => $success,
            'csrf' => csrf_field(),
        ]);
    }

}
