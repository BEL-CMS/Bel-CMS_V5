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

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

use BelCMS\Core\User;
use BelCMS\Core\View;
use BelCMS\Core\Language;

if (!defined('CHECK_INDEX')):
    header(
        $_SERVER['SERVER_PROTOCOL']
        . ' 403 Direct access forbidden'
    );

    exit(
        '<!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>BEL-CMS : Error 403 Forbidden</title>
        </head>
        <body>
            <h1>HTTP Error 403 : Forbidden</h1>
            <p>You don\'t permission to access / on this server.</p>
        </body>
        </html>'
    );
endif;

final class Controller
{
    private Model $model;
    private View $view;
    private Language $language;
    private User $user;

    public function __construct(Model $model,View $view,Language $language,User $user)
    {
        $this->model = $model;
        $this->view = $view;
        $this->language = $language;
        $this->user = $user;
    }
    /**
     * Affichage du profil
     */
    public function index(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

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
                'language' => $this->language,
            ]
        );
    }
    /*
     * Modification du profil
    */

    public function edit(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        $hashKey = $this->user->hashKey();

        if ($hashKey === null || $hashKey === '') {
            header('Location: /profile');
            exit;
        }

        $errors = [];
        $success = null;

        /*
        * =========================================================
        * RÉCUPÉRATION DU PROFIL
        * =========================================================
        */

        $profile = $this->model->getByHashKey($hashKey);

        if (!$profile) {
            echo $this->view->render(
                'Profils',
                'edit',
                [
                    'profile'  => null,
                    'errors'   => ['Profil introuvable.'],
                    'success'  => null,
                    'language' => $this->language,
                ]
            );

            return;
        }
        /*
        * =========================================================
        * TRAITEMENT DU FORMULAIRE
        * =========================================================
        */

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            /*
            * -----------------------------------------------------
            * CSRF
            * -----------------------------------------------------
            */
            if (!csrf_verify($_POST['csrf_token'] ?? null)) {
                $errors[] =
                    'Votre session de sécurité a expiré. Veuillez réessayer.';
            } else {
                /*
                * -------------------------------------------------
                * RÉCUPÉRATION DES CHAMPS
                * -------------------------------------------------
                */
                $username = trim(
                    (string)($_POST['username'] ?? '')
                );
                $lastName = trim(
                    (string)($_POST['last_name'] ?? '')
                );
                $gender = trim(
                    (string)($_POST['gender'] ?? '')
                );
                $publicMail = trim(
                    (string)($_POST['public_mail'] ?? '')
                );
                $websites = trim(
                    (string)($_POST['websites'] ?? '')
                );
                $infosText = trim(
                    (string)($_POST['infos_text'] ?? '')
                );
                $phone = trim(
                    (string)($_POST['phone'] ?? '')
                );
                $birthday = trim(
                    (string)($_POST['birthday'] ?? '')
                );
                $country = trim(
                    (string)($_POST['country'] ?? '')
                );
                $profils = trim(
                    (string)($_POST['profils'] ?? '')
                );
                $gravatar = trim(
                    (string)($_POST['gravatar'] ?? '')
                );
                /*
                * -------------------------------------------------
                * VALIDATION
                * -------------------------------------------------
                */
                if ($username === '') {

                    $errors[] = 'Le nom d’utilisateur est obligatoire.';

                } elseif (mb_strlen($username) < 3) {
                    $errors[] = 'Le nom d’utilisateur doit contenir au moins 3 caractères.';
                } elseif (mb_strlen($username) > 100) {
                    $errors[] = 'Le nom d’utilisateur ne peut pas dépasser 100 caractères.';
                }
                /*
                * Email public facultatif
                */
                if ($publicMail !== '' && !filter_var($publicMail, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'L’adresse e-mail publique est invalide.';
                }
                /*
                * Site web facultatif
                */
                if ($websites !== '' && !filter_var($websites, FILTER_VALIDATE_URL)) {
                    $errors[] = 'L’adresse du site internet est invalide.';
                }
                /*
                * Date de naissance facultative
                */
                if ($birthday !== '') {
                    $date = \DateTime::createFromFormat(
                        'Y-m-d',
                        $birthday
                    );
                    if (!$date || $date->format('Y-m-d') !== $birthday) {
                        $errors[] = 'La date de naissance est invalide.';
                    }
                }
                /*
                * -------------------------------------------------
                * MISE À JOUR
                * -------------------------------------------------
                */
                if (empty($errors)) {
                    $data = [
                        'username'     => $username,
                        'last_name'    => $lastName,
                        'gender'       => $gender,
                        'public_mail'  => $publicMail,
                        'websites'     => $websites,
                        'infos_text'   => $infosText,
                        'phone'        => $phone,
                        'birthday'     => $birthday !== ''
                            ? $birthday
                            : null,
                        'country'      => $country,
                        'profils'      => $profils,
                        'gravatar'     => $gravatar,
                    ];
                    $updated = $this->model->updateProfile(
                        $hashKey,
                        $data
                    );
                    if ($updated) {
                        $success = 'Votre profil a été mis à jour avec succès.';
                        /*
                        * Recharge les données depuis la BDD.
                        */
                        $profile = $this->model->getByHashKey($hashKey);
                    } else {
                        $errors[] = 'Aucune modification n’a pu être enregistrée.';
                    }
                }
            }
        }
        /*
        * =========================================================
        * AFFICHAGE
        * =========================================================
        */
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

public function social(): void
{
    if (!$this->user->isLogged()) {
        header('Location: /user/login');
        exit;
    }

    $hashKey = $this->user->hashKey();

    if ($hashKey === null || $hashKey === '') {
        header('Location: /profils');
        exit;
    }


    echo $this->view->render(
        'Profils',
        'social',
        [
            'social' => $this->model->getSocialByHashKey($hashKey),
            'profile' => $this->user->data()?->profile ?? null,
            'language' => $this->language,
        ]
    );
}

public function editsocial(): void
{
    if (!$this->user->isLogged()) {
        header('Location: /user/login');
        exit;
    }

    $hashKey = $this->user->hashKey();

    if ($hashKey === null || $hashKey === '') {
        header('Location: /profils');
        exit;
    }

    $errors = [];
    $success = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        /*
         * Utilise ici la méthode CSRF déjà employée par ton Controller Profils.
         * Exemple selon ton Core : $this->csrf->validate($_POST['csrf_token'] ?? '')
         */
        $data = [
            'facebook' => $_POST['facebook'] ?? '',
            'youtube' => $_POST['youtube'] ?? '',
            'whatsapp' => $_POST['whatsapp'] ?? '',
            'instagram' => $_POST['instagram'] ?? '',
            'messenger' => $_POST['messenger'] ?? '',
            'tiktok' => $_POST['tiktok'] ?? '',
            'snapchat' => $_POST['snapchat'] ?? '',
            'telegram' => $_POST['telegram'] ?? '',
            'pinterest' => $_POST['pinterest'] ?? '',
            'x_twitter' => $_POST['x_twitter'] ?? '',
            'reddit' => $_POST['reddit'] ?? '',
            'linkedIn' => $_POST['linkedIn'] ?? '',
            'skype' => $_POST['skype'] ?? '',
            'viber' => $_POST['viber'] ?? '',
            'teams_ms' => $_POST['teams_ms'] ?? '',
            'discord' => $_POST['discord'] ?? '',
            'twitch' => $_POST['twitch'] ?? '',
        ];

        foreach ($data as $field => $value) {
            $data[$field] = trim((string)$value);
        }

        if ($this->model->saveSocial($hashKey, $data)) {
            $success = 'Vos réseaux sociaux ont été enregistrés.';
        } else {
            $errors[] = 'Impossible d\'enregistrer vos réseaux sociaux.';
        }
    }

    echo $this->view->render(
        'Profils',
        'editsocial',
        [
            'social' => $this->model->getSocialByHashKey($hashKey),
            'errors' => $errors,
            'success' => $success,
            'language' => $this->language,
        ]
    );
}

}