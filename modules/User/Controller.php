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

namespace BelCMS\Modules\User;

use BelCMS\Core\User;
use BelCMS\Core\View;
use BelCMS\Core\UserSession;

final class Controller
{
    private User $user;
    private View $view;
    private Model $model;
    private UserSession $userSession;

    public function __construct(User $user, View $view, Model $model, UserSession $userSession) 
    {
        $this->user        = $user;
        $this->view        = $view;
        $this->model       = $model;
        $this->userSession = $userSession;
    }
    /**
     * Tableau de bord utilisateur
     */
    public function index(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }
        $user = $this->user->data();
        echo $this->view->render(
            'User',
            'index',
            [
                'user' => $user,
            ]
        );
    }
    /**
     * Connexion utilisateur
     */
    public function login(): void
    {
        if ($this->user->isLogged()) {
            header('Location: /user');
            exit;
        }

        $error    = null;
        $success  = null;
        $username = '';

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $identifier = trim(
                (string)($_POST['identifier'] ?? '')
            );

            $username = $identifier;

            $password = (string)(
                $_POST['password'] ?? ''
            );

            if ($this->user->login($identifier, $password)) {
                if ($this->user->isTwoFactorPending()) {
                    header('Location: /user/login/2fa');
                    exit;
                }

                header('Location: /user');
                exit;
            }

            $error = 'Identifiant ou mot de passe incorrect.';
        }

        echo $this->view->render(
            'User',
            'login',
            [
                'error'    => $error,
                'success'  => $success,
                'username' => $username,
            ]
        );
    }
    /**
     * Déconnexion
     */
    public function logout(): void
    {
        $this->user->logout();
        header('Location: /user/login');
        exit;
    }
    /**
     * Profil utilisateur
     */
    public function profile(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        $user = $this->user->data();

        echo $this->view->render(
            'User',
            'profile',
            [
                'user' => $user,
            ]
        );
    }

    /**
     * Modification du profil
     */
    public function edit(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        $currentUser = $this->user->data();
        $error       = null;
        $success     = null;
        $username    = (string)($currentUser->username ?? '');
        $email       = (string)($currentUser->email ?? '');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            /*
            * Vérification CSRF
            */
            if (!csrf_verify($_POST['csrf_token'] ?? null)) {
                $error = 'Votre session de sécurité a expiré. Veuillez réessayer.';
            } else {
                $username = trim(
                    (string)($_POST['username'] ?? '')
                );
                $email = trim(
                    (string)($_POST['email'] ?? '')
                );
                /*
                * Validation du username
                */
                if ($username === '') {
                    $error = 'Le nom d’utilisateur est obligatoire.';
                } elseif (
                    mb_strlen($username) < 3 ||
                    mb_strlen($username) > 100
                ) {
                    $error = 'Le nom d’utilisateur doit contenir entre 3 et 100 caractères.';
                /*
                * Validation de l'email
                */
                } elseif (
                    $email === '' ||
                    !filter_var(
                        $email,
                        FILTER_VALIDATE_EMAIL
                    )
                ) {
                    $error = 'Veuillez fournir une adresse email valide.';
                } else {
                    /*
                    * Vérification d'un username déjà utilisé
                    */
                    $existingUsername = $this->model->getByUsername(
                        $username
                    );

                    if (
                        $existingUsername &&
                        (int)$existingUsername->id !== $this->user->id()
                    ) {
                        $error = 'Ce nom d’utilisateur est déjà utilisé.';
                    }
                    /*
                    * Vérification d'un email déjà utilisé
                    */
                    if ($error === null) {
                        $existingEmail = $this->model->getByEmail(
                            $email
                        );
                        if (
                            $existingEmail &&
                            (int)$existingEmail->id !== $this->user->id()
                        ) {
                            $error = 'Cette adresse email est déjà utilisée.';
                        }
                    }
                    /*
                    * Mise à jour
                    */
                    if ($error === null) {

                        $updated = $this->model->update(
                            $this->user->id(),
                            [
                                'username' => $username,
                                'email'    => $email,
                            ]
                        );
                        if ($updated) {
                            $success = 'Votre profil a été mis à jour.';
                            /*
                            * Recharge l'utilisateur
                            */
                            $this->user->reload();
                            $currentUser = $this->user->data();
                            $username = (string)(
                                $currentUser->username ?? ''
                            );
                            $email = (string)(
                                $currentUser->email ?? ''
                            );

                        } else {
                            $error = 'Impossible de mettre à jour votre profil.';
                        }
                    }
                }
            }
        }

        echo $this->view->render(
            'User',
            'edit',
            [
                'user'     => $currentUser,
                'username' => $username,
                'email'    => $email,
                'error'    => $error,
                'success'  => $success,
            ]
        );
    }
    /**
     * Modification du mot de passe
     */
    public function password(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /*
            * Vérification CSRF
            */
            if (!csrf_verify($_POST['csrf_token'] ?? null)) {
                $error = 'Votre session de sécurité a expiré. Veuillez réessayer.';
            } else {

                $currentPassword = (string) (
                    $_POST['current_password'] ?? ''
                );

                $newPassword = (string) (
                    $_POST['new_password'] ?? ''
                );

                $confirmPassword = (string) (
                    $_POST['confirm_password'] ?? ''
                );
                /*
                * Vérification de l'ancien mot de passe.
                */
                if (!$this->model->verifyPassword($this->user->id(),$currentPassword)) {
                    $error = 'Votre mot de passe actuel est incorrect.';
                }
                /*
                * Vérification de la longueur.
                */
                elseif (strlen($newPassword) < 8) {

                    $error = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
                }
                /*
                * Vérification de confirmation.
                */
                elseif ($newPassword !== $confirmPassword) {

                    $error = 'Les deux nouveaux mots de passe ne correspondent pas.';
                }
                /*
                * Empêche de remettre exactement le même mot de passe.
                */
                elseif (
                    $currentPassword === $newPassword
                ) {

                    $error = 'Le nouveau mot de passe doit être différent de l’ancien.';
                }
                /*
                * Mise à jour.
                */
                else {
                    $updated = $this->model->updatePassword(
                        $this->user->id(),
                        $newPassword
                    );
                    if ($updated) {

                        $success = 'Votre mot de passe a été modifié avec succès.';

                    } else {

                        $error = 'Impossible de modifier votre mot de passe.';
                    }
                }
            }
        }

        echo $this->view->render(
            'User',
            'password',
            [
                'error'   => $error,
                'success' => $success,
            ]
        );
    }
    /**
     * Sécurité du compte
     */
    public function security(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }

        $user = $this->user->data();

        $recoveryCount =
            $this->user->isTwoFactorEnabled()
                ? $this->user->getRecoveryCodeCount()
                : 0;

        echo $this->view->render(
            'User',
            'security',
            [
                'user'          => $user,
                'recoveryCount' => $recoveryCount,
            ]
        );
    }
    public function twoFactor(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }
        $error = null;
        $success = null;
        /*
        * Désactivation de la 2FA
        */
        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'disable') {
                $password = (string)($_POST['password'] ?? '');
                if (!csrf_verify($_POST['csrf_token'] ?? null)) {
                    $error = 'Votre session de sécurité a expiré. Veuillez réessayer.';
                } else {
                    if ($password === '') {
                        $error = 'Veuillez saisir votre mot de passe.';
                    } elseif (!$this->user->verifyPassword($password)) {
                        $error = 'Mot de passe incorrect.';
                    } elseif (!$this->user->disableTwoFactor()) {
                        $error = 'Impossible de désactiver l’authentification à deux facteurs.';
                    } else {
                        $success = 'L’authentification à deux facteurs a été désactivée.';
                    }

                    echo $this->view->render(
                        'User',
                        '2fa',
                        [
                            'enabled' => $this->user->isTwoFactorEnabled(),
                            'secret'  => null,
                            'uri'     => null,
                            'error'   => $error,
                            'success' => $success
                        ]
                    );

                    return;
                }
            }

        /*
        * Déjà activée
        */
        if ($this->user->isTwoFactorEnabled()) {

            echo $this->view->render(
                'User',
                '2fa',
                [
                    'enabled' => true,
                    'secret'  => null,
                    'uri'     => null,
                    'error'   => null,
                    'success' => null
                ]
            );

            return;
        }

        /*
        * Activation
        */
        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
            && !isset($_POST['action'])
        ) {
            $secret = $this->user->getTwoFactorSetupSecret();

            $code = trim(
                (string)($_POST['code'] ?? '')
            );

            if ($secret === '') {
                $error = 'La configuration de la 2FA a expiré. Veuillez recommencer.';
            } elseif (!preg_match('/^[0-9]{6}$/', $code)) {
                $error = 'Veuillez saisir un code à 6 chiffres.';
            } elseif (!$this->user->verifyTwoFactorCode($secret, $code)) {
                $error = 'Le code de vérification est incorrect.';
            } elseif (!$this->user->enableTwoFactor($secret, $code)) {
                $error = 'Impossible d’activer l’authentification à deux facteurs.';
            } else {
                $this->user->clearTwoFactorSetupSecret();

                $success = 'L’authentification à deux facteurs a été activée.';
            }
        }

        /*
        * Génération du secret
        */
        $secret = $this->user->getTwoFactorSetupSecret();
        if (!$this->user->isTwoFactorEnabled()
            && (
                !is_string($secret)
                || $secret === ''
            )
        ) {
            $secret = $this->user->generateTwoFactorSecret();

            $this->user->setTwoFactorSetupSecret($secret);
        }

        $uri = null;

        if (
            !$this->user->isTwoFactorEnabled()
            && is_string($secret)
            && $secret !== ''
        ) {
            $uri = $this->user->getTwoFactorUri(
                $secret
            );
        }

        echo $this->view->render(
            'User',
            '2fa',
            [
                'enabled' => $this->user->isTwoFactorEnabled(),
                'secret'  => $secret,
                'uri'     => $uri,
                'error'   => $error,
                'success' => $success
            ]
        );
    }
    /**
     * Validation du 2FA lors de la connexion.
     */
    public function twoFactorLogin(): void
    {
        /*
        * Un utilisateur déjà connecté
        * n'a rien à faire ici.
        */
        if ($this->user->isLogged()) {
            header('Location: /user');
            exit;
        }
        /*
        * Vérifie qu'une authentification 2FA
        * est bien en attente.
        */
        if (!$this->user->isTwoFactorPending()) {
            header('Location: /user/login');
            exit;
        }
        $error = null;
        /*
        * Récupération du hash_key en attente.
        */
        $hashKey = $this->user->getTwoFactorPendingHashKey();

        if (
            $hashKey === null ||
            $hashKey === ''
        ) {
            $this->user->clearTwoFactorPending();

            header('Location: /user/login');
            exit;
        }

        /*
        * Récupération de l'utilisateur
        * grâce à son hash_key.
        */
        $user = $this->model->getByHashKey(
            $hashKey
        );

        if (!$user) {
            $this->user->clearTwoFactorPending();

            header('Location: /user/login');
            exit;
        }

        /*
        * Vérifie que le 2FA est toujours actif
        * et qu'un secret existe.
        */
        if (
            empty($user->two_factor_enabled) ||
            empty($user->two_factor_secret)
        ) {
            $this->user->clearTwoFactorPending();

            header('Location: /user/login');
            exit;
        }

        /*
        * =========================================================
        * TRAITEMENT DU FORMULAIRE
        * =========================================================
        */
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            /*
            * Code Google Authenticator.
            */
            $code = trim(
                (string) ($_POST['code'] ?? '')
            );

            /*
            * Code de récupération.
            */
            $recoveryCode = trim(
                (string) ($_POST['recovery_code'] ?? '')
            );

            /*
            * =====================================================
            * GOOGLE AUTHENTICATOR
            * =====================================================
            */
            if ($code !== '') {

                /*
                * Vérification du format.
                */
                if (
                    !preg_match(
                        '/^[0-9]{6}$/',
                        $code
                    )
                ) {

                    $error =
                        'Veuillez saisir un code à 6 chiffres.';

                /*
                * Vérification TOTP.
                */
                } elseif (!$this->user->verifyTwoFactorCode($code, $user->two_factor_secret)
                ) {
                    $error =
                        'Le code de vérification est incorrect.';
                /*
                * Finalisation de la connexion.
                */
                } elseif (
                    !$this->user->completeTwoFactorLogin()
                ) {

                    $error =
                        'Impossible de finaliser la connexion.';

                } else {

                    header('Location: /user');
                    exit;
                }

            /*
            * =====================================================
            * CODE DE RÉCUPÉRATION
            * =====================================================
            */
            } elseif ($recoveryCode !== '') {
                /*
                * Vérifie et consomme le code.
                */
                if (
                    !$this->user->verifyRecoveryCode(
                        $recoveryCode
                    )
                ) {
                    $error =
                        'Le code de récupération est incorrect '
                        . 'ou a déjà été utilisé.';

                /*
                * Finalisation de la connexion.
                */
                } elseif (
                    !$this->user->completeTwoFactorLogin()
                ) {
                    $error =
                        'Impossible de finaliser la connexion.';
                } else {
                    header('Location: /user');
                    exit;
                }
            } else {
                $error = 'Veuillez saisir un code de sécurité.';
            }
        }
        /*
        * Affichage de la page de validation 2FA.
        */
        echo $this->view->render(
            'User',
            'login-2fa',
            [
                'error' => $error,
            ]
        );
    }
    /**
     * Gestion des codes de récupération.
     */
    public function recovery(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }
        /*
        * Les codes de récupération nécessitent
        * que le 2FA soit activé.
        */
        if (!$this->user->canManageRecoveryCodes()) {
            header('Location: /user/security');
            exit;
        }
        $error = null;
        $success = null;
        $recoveryCodes = [];
        /*
        * Nombre de codes actuellement disponibles.
        */
        $count = $this->user->getRecoveryCodeCount();
        /*
        * Génération d'un nouveau jeu de codes.
        */
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!csrf_verify($_POST['csrf_token'] ?? null)) {
                $error = 'Votre session de sécurité a expiré. Veuillez réessayer.';
            } else {
                $recoveryCodes =
                    $this->user->generateRecoveryCodes(10);

                if (count($recoveryCodes) === 10) {

                    $success =
                        '10 nouveaux codes de récupération ont été générés. '
                        . 'Les anciens codes ne sont désormais plus valides.';

                    $count = 10;

                } else {

                    $error =
                        'Impossible de générer les codes de récupération.';
                }
            }
        }

        echo $this->view->render(
            'User',
            'recovery',
            [
                'count'         => $count,
                'error'         => $error,
                'success'       => $success,
                'recoveryCodes' => $recoveryCodes,
            ]
        );
    }
    public function sessions(): void
    {
        if (!$this->user->isLogged()) {
            header('Location: /user/login');
            exit;
        }
        $hashKey = $this->user->hashKey();

        if ($hashKey === null || $hashKey === '') {
            header('Location: /user/security');
            exit;
        }
        $success = null;
        $error   = null;
        $currentSessionId = session_id();
        /*
        * Déconnexion de toutes les autres sessions
        */
        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
            && isset($_POST['action'])
            && $_POST['action'] === 'logout-others'
        ) {
            $deleted = $this->userSession->deleteOthers(
                $hashKey,
                $currentSessionId
            );
            $success = $deleted > 0
                ? $deleted . ' session(s) ont été déconnectée(s).'
                : 'Aucune autre session active.';
        }

        /*
        * Récupération des sessions
        */
        $sessions = $this->userSession->getByHashKey($hashKey);
        /*
        * Nettoyage des données destinées à la vue
        */
        foreach ($sessions as $session) {
            $session->is_current = (
                isset($session->session_id)
                && $session->session_id === $currentSessionId
            );
        }

        echo $this->view->render(
            'User',
            'sessions',
            [
                'sessions'          => $sessions,
                'currentSessionId'  => $currentSessionId,
                'success'           => $success,
                'error'             => $error,
            ]
        );
    }
public function register(): void
    {
        if ($this->user->isLogged()) {
            header('Location: /user');
            exit;
        }

        $errors = [];
        $success = null;

        $username = '';
        $email = '';
        $validationLink = null;

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

            /*
             * =========================================================
             * CSRF
             * =========================================================
             */
            if (!csrf_verify($_POST['csrf_token'] ?? null)) {
                $errors[] =
                    'La session de sécurité a expiré. Veuillez réessayer.';
            }

            $username = trim(
                (string)($_POST['username'] ?? '')
            );

            $email = trim(
                (string)($_POST['email'] ?? '')
            );

            $password = (string)(
                $_POST['password'] ?? ''
            );

            $passwordConfirm = (string)(
                $_POST['password_confirm'] ?? ''
            );

            /*
             * =========================================================
             * VALIDATION
             * =========================================================
             */
            if ($username === '') {
                $errors[] =
                    'Le nom d’utilisateur est obligatoire.';
            } elseif (mb_strlen($username) < 3) {
                $errors[] =
                    'Le nom d’utilisateur doit contenir au moins 3 caractères.';
            } elseif (mb_strlen($username) > 100) {
                $errors[] =
                    'Le nom d’utilisateur ne peut pas dépasser 100 caractères.';
            } elseif (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
                $errors[] =
                    'Le nom d’utilisateur contient des caractères non autorisés.';
            }

            if ($email === '') {
                $errors[] =
                    'L’adresse e-mail est obligatoire.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] =
                    'L’adresse e-mail est invalide.';
            }

            if ($password === '') {
                $errors[] =
                    'Le mot de passe est obligatoire.';
            } elseif (strlen($password) < 8) {
                $errors[] =
                    'Le mot de passe doit contenir au moins 8 caractères.';
            }

            if ($password !== $passwordConfirm) {
                $errors[] =
                    'Les deux mots de passe ne correspondent pas.';
            }

            /*
             * =========================================================
             * DOUBLONS
             * =========================================================
             */
            if (empty($errors)) {

                $existingUsername =
                    $this->model->getByUsername($username);

                if ($existingUsername) {
                    $errors[] =
                        'Ce nom d’utilisateur est déjà utilisé.';
                }

                if (empty($errors)) {

                    $existingEmail =
                        $this->model->getByEmail($email);

                    if ($existingEmail) {
                        $errors[] =
                            'Cette adresse e-mail est déjà utilisée.';
                    }
                }

                if (empty($errors)) {

                    $temporaryUsername =
                        $this->model->getTemporaryByUsername($username);

                    if ($temporaryUsername) {
                        $errors[] =
                            'Une inscription est déjà en attente pour ce nom d’utilisateur.';
                    }
                }

                if (empty($errors)) {

                    $temporaryEmail =
                        $this->model->getTemporaryByEmail($email);

                    if ($temporaryEmail) {
                        $errors[] =
                            'Une inscription est déjà en attente pour cette adresse e-mail.';
                    }
                }
            }

            /*
             * =========================================================
             * CRÉATION DANS belcms_user_temp
             * =========================================================
             */
            if (empty($errors)) {

                $hashKey = bin2hex(
                    random_bytes(16)
                );

                $validationKey = bin2hex(
                    random_bytes(32)
                );

                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                if ($passwordHash === false) {

                    $errors[] =
                        'Impossible de sécuriser le mot de passe.';

                } else {

                    $expiresAt = date(
                        'Y-m-d H:i:s',
                        strtotime('+24 hours')
                    );

                    $remoteIp = $_SERVER['REMOTE_ADDR'] ?? null;

                    $created = $this->model->createTemporary(
                        $username,
                        $hashKey,
                        $email,
                        $passwordHash,
                        is_string($remoteIp) ? $remoteIp : null,
                        $validationKey,
                        $expiresAt
                    );

                    if (!$created) {

                        $errors[] =
                            'Impossible d’enregistrer votre inscription.';

                    } else {

                        /*
                         * =================================================
                         * LIEN DE VALIDATION
                         * =================================================
                         */
                        $scheme = (
                            (
                                !empty($_SERVER['HTTPS'])
                                && $_SERVER['HTTPS'] !== 'off'
                            )
                            ||
                            (
                                isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
                                && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
                            )
                        ) ? 'https' : 'http';

                        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

                        $validationLink =
                            $scheme
                            . '://'
                            . $host
                            . '/user/verify?key='
                            . urlencode($validationKey);

                        /*
                         * =================================================
                         * E-MAIL DE VALIDATION
                         *
                         * Le compte reste TOUJOURS dans belcms_user_temp
                         * tant que le lien n'a pas été validé.
                         * Un échec de mail ne supprime donc PAS l'inscription.
                         * =================================================
                         */
                        $subject =
                            'Validez votre inscription sur Bel-CMS';

                        $message = <<<MAIL
Bonjour {$username},

Votre inscription sur Bel-CMS a bien été enregistrée.

Pour valider votre inscription et activer votre compte, veuillez utiliser le lien suivant :

{$validationLink}

Ce lien de validation est valable pendant 24 heures.

Si vous n'êtes pas à l'origine de cette inscription, vous pouvez ignorer cet e-mail.

Cordialement,

L'équipe Bel-CMS
https://bel-cms.dev
MAIL;

                        $headers = [
                            'MIME-Version: 1.0',
                            'Content-Type: text/plain; charset=UTF-8',
                            'From: Bel-CMS <noreply@bel-cms.dev>',
                            'Reply-To: noreply@bel-cms.dev',
                        ];

                        $mailSent = @mail(
                            $email,
                            $subject,
                            $message,
                            implode("\r\n", $headers)
                        );

                        $isLocalhost = in_array(
                            strtolower($host),
                            [
                                'localhost',
                                '127.0.0.1',
                                '::1',
                            ],
                            true
                        );

                        if ($mailSent) {

                            $success =
                                'Votre inscription est en attente de validation. '
                                . 'Un e-mail de validation vient de vous être envoyé.';

                            /*
                             * Le lien ne doit plus être affiché une fois
                             * que l'e-mail a bien été envoyé.
                             */
                            $validationLink = null;

                            $username = '';
                            $email = '';

                        } elseif ($isLocalhost) {

                            /*
                             * =================================================
                             * MODE LOCALHOST
                             *
                             * Aucun SMTP n'est configuré : on garde
                             * l'inscription en attente et on affiche le lien
                             * pour permettre le test de la validation.
                             * =================================================
                             */
                            $success =
                                'Inscription enregistrée en attente de validation. '
                                . 'Mode développement : utilisez le lien ci-dessous pour valider le compte.';

                        } else {

                            /*
                             * En production, le compte reste également
                             * en attente afin qu'un administrateur puisse
                             * intervenir manuellement si nécessaire.
                             */
                            $errors[] =
                                'Votre inscription est enregistrée, mais '
                                . 'l’e-mail de validation n’a pas pu être envoyé. '
                                . 'Un administrateur pourra valider le compte manuellement.';

                            $success = null;
                        }
                    }
                }
            }
        }

        echo $this->view->render(
            'User',
            'register',
            [
                'errors'         => $errors,
                'success'        => $success,
                'username'       => $username,
                'email'          => $email,
                'validationLink' => $validationLink,
            ]
        );
    }

    /**
     * Validation d'une inscription temporaire.
     */
    public function verify(): void
    {
        if ($this->user->isLogged()) {
            header('Location: /user');
            exit;
        }

        $error = null;
        $success = null;

        $validationKey = trim(
            (string)($_GET['key'] ?? '')
        );

        if (
            $validationKey === ''
            || !preg_match('/^[a-f0-9]{64}$/i', $validationKey)
        ) {
            $error =
                'Le lien de validation est invalide.';

        } else {

            $temporaryUser =
                $this->model->getTemporaryByValidationKey(
                    $validationKey
                );

            if (!$temporaryUser) {

                $error =
                    'Cette inscription est introuvable ou le lien a déjà été utilisé.';

            } elseif (
                !empty($temporaryUser->expires_at)
                && strtotime((string)$temporaryUser->expires_at) <= time()
            ) {

                $this->model->deleteTemporary(
                    $validationKey
                );

                $error =
                    'Ce lien de validation a expiré. Veuillez recommencer votre inscription.';

            } elseif (
                !$this->model->createFromTemporary(
                    $temporaryUser
                )
            ) {

                $error =
                    'Impossible de finaliser la création de votre compte.';

            } else {

                /*
                 * =========================================================
                 * LE COMPTE EST MAINTENANT VALIDÉ
                 * =========================================================
                 */

                $this->model->deleteTemporary(
                    $validationKey
                );

                /*
                 * =========================================================
                 * MAIL DE CONFIRMATION APRÈS VALIDATION
                 * =========================================================
                 */
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

                $subject =
                    'Votre compte Bel-CMS est validé';

                $username =
                    (string)($temporaryUser->username ?? '');

                $email =
                    (string)($temporaryUser->email ?? '');

                $loginUrl =
                    'https://'
                    . $host
                    . '/user/login';

                /*
                 * En localhost, on utilise le protocole courant.
                 */
                if (
                    $host === 'localhost'
                    || $host === '127.0.0.1'
                    || $host === '::1'
                ) {
                    $scheme = (
                        !empty($_SERVER['HTTPS'])
                        && $_SERVER['HTTPS'] !== 'off'
                    ) ? 'https' : 'http';

                    $loginUrl =
                        $scheme
                        . '://'
                        . $host
                        . '/user/login';
                }

                $message = <<<MAIL
Bonjour {$username},

Votre compte Bel-CMS a été validé avec succès.

Vous pouvez maintenant vous connecter à votre espace personnel :

{$loginUrl}

Merci pour votre inscription.

Cordialement,

L'équipe Bel-CMS
https://bel-cms.dev
MAIL;

                $headers = [
                    'MIME-Version: 1.0',
                    'Content-Type: text/plain; charset=UTF-8',
                    'From: Bel-CMS <noreply@bel-cms.dev>',
                    'Reply-To: noreply@bel-cms.dev',
                ];

                /*
                 * L'échec du mail de confirmation ne remet PAS
                 * en cause la validation du compte.
                 */
                @mail(
                    $email,
                    $subject,
                    $message,
                    implode("\r\n", $headers)
                );

                $success =
                    'Votre compte a été validé avec succès. '
                    . 'Vous pouvez maintenant vous connecter.';
            }
        }

        echo $this->view->render(
            'User',
            'verify',
            [
                'error'   => $error,
                'success' => $success,
            ]
        );
    }

}