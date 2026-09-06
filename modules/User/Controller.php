<?php

declare(strict_types=1);

namespace BelCMS\Modules\User;

use BelCMS\Core\User;
use BelCMS\Core\View;

final class Controller
{
    private User $user;
    private View $view;
    private Model $model;

    public function __construct(
        User $user,
        View $view,
        Model $model
    ) {
        $this->user  = $user;
        $this->view  = $view;
        $this->model = $model;
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

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $identifier = trim(
                (string) ($_POST['identifier'] ?? '')
            );

            $password = (string) (
                $_POST['password'] ?? ''
            );

            /*
            * Tentative de connexion
            */
            if (
                $this->user->login(
                    $identifier,
                    $password
                )
            ) {

                /*
                * Le mot de passe est correct,
                * mais un 2FA est nécessaire.
                */
                if ($this->user->isTwoFactorPending()) {

                    header('Location: /user/login/2fa');
                    exit;
                }

                /*
                * Connexion classique
                */
                header('Location: /user');
                exit;
            }

            $error = 'Identifiant ou mot de passe incorrect.';
        }

        echo $this->view->render(
            'User',
            'login',
            [
                'error' => $error,
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

        $error = null;
        $success = null;

        $username = (string) ($currentUser->username ?? '');
        $email    = (string) ($currentUser->email ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim(
                (string) ($_POST['username'] ?? '')
            );

            $email = trim(
                (string) ($_POST['email'] ?? '')
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
                * Vérification d'un username déjà utilisé.
                */
                $existingUsername = $this->model->getByUsername(
                    $username
                );

                if (
                    $existingUsername &&
                    (int) $existingUsername->id !== $this->user->id()
                ) {

                    $error = 'Ce nom d’utilisateur est déjà utilisé.';
                }

                /*
                * Vérification d'un email déjà utilisé.
                */
                if ($error === null) {

                    $existingEmail = $this->model->getByEmail(
                        $email
                    );

                    if (
                        $existingEmail &&
                        (int) $existingEmail->id !== $this->user->id()
                    ) {

                        $error = 'Cette adresse email est déjà utilisée.';
                    }
                }

                /*
                * Mise à jour.
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
                        * On recharge l'utilisateur
                        * présent en mémoire afin que
                        * User::data() soit immédiatement
                        * à jour.
                        */
                        $this->user->reload();

                        $currentUser = $this->user->data();

                        $username = (string) (
                            $currentUser->username ?? ''
                        );

                        $email = (string) (
                            $currentUser->email ?? ''
                        );

                    } else {

                        $error = 'Impossible de mettre à jour votre profil.';
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
            if (!$this->model->verifyPassword(
                $this->user->id(),
                $currentPassword
            )) {

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

        /**
         * Si le 2FA est déjà activé.
         */
if ($this->user->isTwoFactorEnabled()) {

    echo $this->view->render(
        'User',
        '2fa',
        [
            'enabled' => true,
        ]
    );

    return;
}

        $error = null;
        $success = null;

        /**
         * ---------------------------------------------------------
         * POST : validation du code TOTP
         * ---------------------------------------------------------
         */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $code = trim(
                (string) ($_POST['code'] ?? '')
            );

            /**
             * Le secret doit venir de la session
             * et non du formulaire.
             */
            $secret = $_SESSION[
                'BELCMS_2FA_SETUP_SECRET'
            ] ?? null;

            if (
                !is_string($secret) ||
                $secret === ''
            ) {

                $error =
                    'La configuration 2FA a expiré. '
                    . 'Veuillez recommencer.';

            } elseif (
                !preg_match(
                    '/^[0-9]{6}$/',
                    $code
                )
            ) {

                $error =
                    'Veuillez saisir un code à 6 chiffres.';

            } elseif (
                !$this->user->verifyTwoFactorCode(
                    $code,
                    $secret
                )
            ) {

                $error =
                    'Le code de vérification est incorrect.';

            } else {

                /**
                 * Code valide :
                 * enregistrement définitif du secret.
                 */
                if (
                    $this->user->enableTwoFactor(
                        $secret,
                        $code
                    )
                ) {

                    /**
                     * Génération des codes de récupération.
                     */
                    $recoveryCodes =
                        $this->user->generateRecoveryCodes(10);

                    /**
                     * Le secret temporaire n'est plus nécessaire.
                     */
                    unset(
                        $_SESSION[
                            'BELCMS_2FA_SETUP_SECRET'
                        ]
                    );

                    $success =
                        'L’authentification à deux facteurs '
                        . 'est maintenant activée.';

                    echo $this->view->render(
                        'User',
                        '2fa',
                        [
                            'enabled'       => true,
                            'success'       => $success,
                            'recoveryCodes' => $recoveryCodes,
                            'showRecovery'  => true,
                        ]
                    );

                    return;
                }

                $error =
                    'Impossible d’activer '
                    . 'l’authentification à deux facteurs.';
            }
        }

        /**
         * ---------------------------------------------------------
         * GET : création/récupération du secret temporaire
         * ---------------------------------------------------------
         */

        $secret = $_SESSION[
            'BELCMS_2FA_SETUP_SECRET'
        ] ?? null;

        if (
            !is_string($secret) ||
            $secret === ''
        ) {

            $secret =
                $this->user->generateTwoFactorSecret();

            $_SESSION[
                'BELCMS_2FA_SETUP_SECRET'
            ] = $secret;
        }

        $uri = $this->user->getTwoFactorUri(
            $this->user->email(),
            $secret
        );

        echo $this->view->render(
            'User',
            '2fa',
            [
                'enabled' => false,
                'secret'  => $secret,
                'uri'     => $uri,
                'error'   => $error,
                'success' => $success,
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
        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
        ) {

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
                } elseif (
                    !$this->user->verifyTwoFactorCode(
                        $code,
                        $user->two_factor_secret
                    )
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

                $error =
                    'Veuillez saisir un code de sécurité.';
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
        if (
            ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
        ) {

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
}