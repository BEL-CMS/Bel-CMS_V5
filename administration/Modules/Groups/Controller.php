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

namespace BelCMS\Admin\Groups;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

use BelCMS\Core\Assets;
use BelCMS\Core\Csrf;
use BelCMS\Core\Errors\ErrorPage;
use BelCMS\Core\View;

final class Controller
{
    public function __construct(
        private View $view,
        private Assets $assets,
        private Model $model,
        private Csrf $csrf,
        private ErrorPage $errorPage
    ) {
    }
    /**
     * Liste des groupes
     */
    public function index(): void
    {
        $groups = $this->model->getGroups();
        $this->assets->css(
            '/administration/Modules/Groups/css/groups.css'
        );
        $this->assets->css(
            '/administration/Modules/Groups/css/groups-add-member.css'
        );
        echo $this->view->renderAdmin(
            'Groups',
            'index',
            [
                'groups' => $groups,
                'csrf'   => $this->csrf->field(),
            ]
        );
    }
    /**
     * Création d'un groupe
     */
    public function create(): void
    {
        /*
        * CSS du module
        */
        $this->assets->css(
            '/administration/Modules/Groups/css/groups.css'
        );
        $this->assets->css(
            '/administration/Modules/Groups/css/groups-add-member.css'
        );
        /*
        * Affichage du formulaire
        */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo $this->view->renderAdmin(
                'Groups',
                'create',
                [
                    'csrf' => $this->csrf->field(),
                ]
            );
            return;
        }
        /*
        * Vérification CSRF
        */
        if (!$this->csrf->verify($_POST['csrf_token'] ?? '')) {
            $this->errorPage->show(
                419,
                'Session expirée',
                'Votre session a expiré. Veuillez recommencer.',
                '/admin/groups/create',
                'Retour'
            );
        }
        /*
        * Récupération des données
        */
        $name = trim(
            (string) ($_POST['name'] ?? '')
        );
        $image = trim(
            (string) ($_POST['image'] ?? '')
        );
        $color = trim(
            (string) ($_POST['color'] ?? '#000000')
        );
        $description = trim(
            (string) ($_POST['description'] ?? '')
        );
        /*
        * Validation du nom
        */
        if ($name === '') {
            $this->errorPage->show(
                400,
                'Nom invalide',
                'Le nom du groupe est obligatoire.',
                '/admin/groups/create',
                'Retour'
            );
        }
        /*
        * Longueur du nom
        */
        if (mb_strlen($name) > 32) {
            $this->errorPage->show(
                400,
                'Nom invalide',
                'Le nom du groupe ne peut pas dépasser 32 caractères.',
                '/admin/groups/create',
                'Retour'
            );
        }
        if (!preg_match('/^#[a-fA-F0-9]{6,8}$/', $color)) {
            $this->errorPage->show(
                400,
                'Couleur invalide',
                'La couleur du groupe est invalide.',
                '/admin/groups/create',
                'Retour'
            );
        }
        $idGroup = $this->model->getNextGroupId();
        /*
        * Création du groupe
        */
        $created = $this->model->createGroup(
            [
                'name' => $name,
                'id_group' => $idGroup,
                'image' => $image,
                'color' => $color,
                'description' => $description,
            ]
        );
        /*
        * Vérification de la création
        */
        if (!$created) {
            $this->errorPage->show(
                500,
                'Erreur',
                'Impossible de créer le groupe.',
                '/admin/groups/create',
                'Retour'
            );
        }
        /*
        * Régénération du token CSRF
        *
        * On évite de réutiliser le même token
        * après une opération réussie.
        */
        $this->csrf->regenerate();
        /*
        * Retour vers la liste des groupes
        */
        header('Location: /admin/groups');
        exit;
    }
    /**
     * Modification d'un groupe
     */
    public function edit(): void
    {
        $this->assets->css(
            '/administration/Modules/Groups/css/groups.css'
        );
        $this->assets->css(
            '/administration/Modules/Groups/css/groups-add-member.css'
        );
        $idGroup = (int) (
            $_GET['id_group'] ?? 0
        );
        if ($idGroup <= 0) {
            $this->errorPage->show(
                400,
                'Groupe invalide',
                'L’identifiant du groupe est invalide.',
                '/admin/groups',
                'Retour aux groupes'
            );
        }
        /*
         * Récupération du groupe
         */
        $group = $this->model->getGroupByIdGroup(
            $idGroup
        );
        if (!$group) {
            $this->errorPage->show(
                404,
                'Groupe introuvable',
                'Le groupe demandé n’existe pas.',
                '/admin/groups',
                'Retour aux groupes'
            );
        }
        /*
         * Traitement du formulaire
         */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->csrf->verify($_POST['csrf_token'] ?? '')) {
                $this->errorPage->show(
                    419,
                    'Session expirée',
                    'Votre session a expiré. Veuillez actualiser la page et réessayer.'
                );
            }
            $name = trim(
                (string) ($_POST['name'] ?? '')
            );
            $image = trim(
                (string) ($_POST['image'] ?? '')
            );
            $color = trim(
                (string) ($_POST['color'] ?? '#000000')
            );
            $description = trim(
                (string) ($_POST['description'] ?? '')
            );
            /*
             * Validation
             */
            if ($name === '') {
                $this->errorPage->show(
                    400,
                    'Nom invalide',
                    'Le nom du groupe est obligatoire.',
                    '/admin/groups/edit?id_group=' . $idGroup,
                    'Retour'
                );
            }
            if (mb_strlen($name) > 32) {
                $this->errorPage->show(
                    400,
                    'Nom invalide',
                    'Le nom du groupe ne peut pas dépasser 32 caractères.',
                    '/admin/groups/edit?id_group=' . $idGroup,
                    'Retour'
                );
            }
            if (!preg_match('/^#[a-fA-F0-9]{6,8}$/', $color)) {
                $this->errorPage->show(
                    400,
                    'Couleur invalide',
                    'La couleur du groupe est invalide.',
                    '/admin/groups/edit?id_group=' . $idGroup,
                    'Retour'
                );
            }
            /*
             * Vérification du nom
             */
            if (
                $this->model->groupNameExists(
                    $name,
                    $idGroup
                )
            ) {
                $this->errorPage->show(
                    400,
                    'Groupe existant',
                    'Un autre groupe utilise déjà ce nom.',
                    '/admin/groups/edit?id_group=' . $idGroup,
                    'Retour'
                );
            }
            /*
             * Modification.
             *
             * id_group reste inchangé.
             */
            $updated = $this->model->updateGroup(
                $idGroup,
                [
                    'name'        => $name,
                    'image'       => $image,
                    'color'       => $color,
                    'description' => $description,
                ]
            );
            if (!$updated) {
                $this->errorPage->show(
                    500,
                    'Erreur',
                    'Impossible de modifier le groupe.',
                    '/admin/groups',
                    'Retour aux groupes'
                );
            }
            header('Location: /admin/groups');
            exit;
        }
        /*
         * Affichage du formulaire
         */
        echo $this->view->renderAdmin(
            'Groups',
            'edit',
            [
                'group'    => $group,
                'csrf'     => $this->csrf->field(),
                'isSystem' => in_array(
                    $idGroup,
                    [1, 2],
                    true
                ),
            ]
        );
    }
    /**
     * Suppression d'un groupe
     */
    public function delete(): void
    {
        /*
         * POST uniquement
         */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->errorPage->show(
                405,
                'Méthode non autorisée',
                'Cette action doit être effectuée avec une requête POST.',
                '/admin/groups',
                'Retour aux groupes'
            );
        }
        /*
         * CSRF
         */
        if (!$this->csrf->verify($_POST['csrf_token'] ?? '')) {
            $this->errorPage->show(
                419,
                'Session expirée',
                'Votre session a expiré. Veuillez recommencer.',
                '/admin/groups/create',
                'Retour'
            );
        }
        $idGroup = (int) (
            $_POST['id_group'] ?? 0
        );
        if ($idGroup <= 0) {
            $this->errorPage->show(
                400,
                'Groupe invalide',
                'L’identifiant du groupe est invalide.',
                '/admin/groups',
                'Retour aux groupes'
            );
        }
        /*
         * Protection des groupes système.
         */
        if (in_array($idGroup, [1, 2], true)) {
            $this->errorPage->show(
                403,
                'Action refusée',
                'Les groupes système ne peuvent pas être supprimés.',
                '/admin/groups',
                'Retour aux groupes'
            );
        }
        /*
         * Vérification du groupe.
         */
        $group = $this->model->getGroupByIdGroup(
            $idGroup
        );
        if (!$group) {
            $this->errorPage->show(
                404,
                'Groupe introuvable',
                'Le groupe demandé n’existe pas.',
                '/admin/groups',
                'Retour aux groupes'
            );
        }
        /*
         * Vérification des membres.
         */
        $memberCount = $this->model->countGroupMembers(
            $idGroup
        );
        if ($memberCount > 0) {
            $this->errorPage->show(
                400,
                'Suppression impossible',
                'Ce groupe contient encore des membres. Retirez d’abord les membres du groupe.',
                '/admin/groups',
                'Retour aux groupes'
            );
        }
        /*
         * Suppression.
         */
        $deleted = $this->model->deleteGroup(
            $idGroup
        );
        if (!$deleted) {
            $this->errorPage->show(
                500,
                'Erreur',
                'Impossible de supprimer le groupe.',
                '/admin/groups',
                'Retour aux groupes'
            );
        }
        header('Location: /admin/groups');
        exit;
    }
    /**
     * Liste des membres d'un groupe.
     */
    public function members(): void
    {
        $idGroup = (int) ($_GET['id_group'] ?? 0);
        if ($idGroup <= 0) {
            $this->errorPage->show(
                400,
                'Groupe invalide',
                'L’identifiant du groupe est invalide.',
                '/admin/groups',
                'Retour aux groupes'
            );
            return;
        }
        $group = $this->model->getGroupByIdGroup($idGroup);
        if ($group === null) {
            $this->errorPage->show(
                404,
                'Groupe introuvable',
                'Le groupe demandé n’existe pas.',
                '/admin/groups',
                'Retour aux groupes'
            );
            return;
        }
        $members = $this->model->getGroupMembers($idGroup);
        $this->assets->css(
            '/administration/Modules/Groups/css/groups.css'
        );
        echo $this->view->renderAdmin(
            'Groups',
            'members',
            [
                'group'   => $group,
                'members' => $members,
                'csrf'    => $this->csrf->field(),
            ]
        );
    }
    /**
     * Ajout d'un membre à un groupe.
     */
    public function addMember(): void
    {
        $idGroup = (int) (
            $_GET['id_group']
            ?? $_POST['id_group']
            ?? 0
        );
        if ($idGroup <= 0) {
            $this->errorPage->show(
                400,
                'Groupe invalide',
                'L’identifiant du groupe est invalide.',
                '/admin/groups',
                'Retour aux groupes'
            );
            return;
        }
        $group = $this->model->getGroupByIdGroup($idGroup);
        if ($group === null) {
            $this->errorPage->show(
                404,
                'Groupe introuvable',
                'Le groupe demandé n’existe pas.',
                '/admin/groups',
                'Retour aux groupes'
            );
            return;
        }
        /*
        * Traitement du formulaire
        */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            /*
            * Vérification CSRF
            */
            if (!$this->csrf->verify($_POST['csrf_token'] ?? null)) {
                $this->errorPage->show(
                    419,
                    'Session expirée',
                    'Votre session a expiré. Veuillez actualiser la page et réessayer.',
                    '/admin/groups/members/add?id_group=' . $idGroup,
                    'Retour'
                );
                return;
            }
            $hashKey = trim(
                (string) ($_POST['hash_key'] ?? '')
            );
            /*
            * Aucun utilisateur sélectionné
            */
            if ($hashKey === '') {
                $search = trim(
                    (string) ($_POST['search'] ?? '')
                );
                $this->assets->css(
                    '/administration/Modules/Groups/css/groups.css'
                );
                $this->assets->css(
                    '/administration/Modules/Groups/css/groups-add-member.css'
                );
                echo $this->view->renderAdmin(
                    'Groups',
                    'add_member',
                    [
                        'group'  => $group,
                        'users'  => $this->model->searchUsersForGroup(
                            $idGroup,
                            $search
                        ),
                        'search' => $search,
                        'csrf'   => $this->csrf->field(),
                        'error'  => 'Veuillez sélectionner un utilisateur.',
                    ]
                );
                return;
            }
            /*
            * Ajout du membre
            */
            if (!$this->model->addMemberToGroup(
                $hashKey,
                $idGroup
            )) {
                $search = trim(
                    (string) ($_POST['search'] ?? '')
                );
                $this->assets->css(
                    '/administration/Modules/Groups/css/groups.css'
                );
                $this->assets->css(
                    '/administration/Modules/Groups/css/groups-add-member.css'
                );
                echo $this->view->renderAdmin(
                    'Groups',
                    'add_member',
                    [
                        'group'  => $group,
                        'users'  => $this->model->searchUsersForGroup(
                            $idGroup,
                            $search
                        ),
                        'search' => $search,
                        'csrf'   => $this->csrf->field(),
                        'error'  => 'Impossible d’ajouter cet utilisateur au groupe.',
                    ]
                );
                return;
            }
            /*
            * Régénération du token CSRF
            */
            $this->csrf->regenerate();
            /*
            * Retour aux membres
            */
            header(
                'Location: /admin/groups/members?id_group='
                . $idGroup
            );
            exit;
        }
        /*
        * Recherche
        */
        $search = trim(
            (string) ($_GET['search'] ?? '')
        );
        $users = $this->model->searchUsersForGroup(
            $idGroup,
            $search
        );
        $this->assets->css(
            '/administration/Modules/Groups/css/groups.css'
        );
        $this->assets->css(
            '/administration/Modules/Groups/css/groups-add-member.css'
        );
        echo $this->view->renderAdmin(
            'Groups',
            'add_member',
            [
                'group'  => $group,
                'users'  => $users,
                'search' => $search,
                'csrf'   => $this->csrf->field(),
                'error'  => null,
            ]
        );
    }
    /**
     * Retire un membre d'un groupe.
     */
    public function removeMember(): void
    {
        /*
        * POST uniquement
        */
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->errorPage->show(
                405,
                'Méthode non autorisée',
                'Cette action doit être effectuée avec une requête POST.',
                '/admin/groups',
                'Retour aux groupes'
            );
            return;
        }
        /*
        * Vérification CSRF
        */
        if (!$this->csrf->verify($_POST['csrf_token'] ?? null)) {
            $this->errorPage->show(
                419,
                'Session expirée',
                'Votre session a expiré. Veuillez actualiser la page et réessayer.',
                '/admin/groups',
                'Retour aux groupes'
            );
            return;
        }
        /*
        * Récupération des données
        */
        $idGroup = (int) (
            $_POST['id_group'] ?? 0
        );
        $hashKey = trim(
            (string) ($_POST['hash_key'] ?? '')
        );
        /*
        * Validation
        */
        if ($idGroup <= 0) {
            $this->errorPage->show(
                400,
                'Groupe invalide',
                'L’identifiant du groupe est invalide.',
                '/admin/groups',
                'Retour aux groupes'
            );
            return;
        }
        if ($hashKey === '') {
            $this->errorPage->show(
                400,
                'Utilisateur invalide',
                'L’identifiant de l’utilisateur est invalide.',
                '/admin/groups/members?id_group=' . $idGroup,
                'Retour aux membres'
            );
            return;
        }
        /*
        * Vérification du groupe
        */
        $group = $this->model->getGroupByIdGroup(
            $idGroup
        );
        if ($group === null) {
            $this->errorPage->show(
                404,
                'Groupe introuvable',
                'Le groupe demandé n’existe pas.',
                '/admin/groups',
                'Retour aux groupes'
            );
            return;
        }
        /*
        * Suppression du membre
        */
        if (!$this->model->removeMemberFromGroup(
            $hashKey,
            $idGroup
        )) {
            $this->errorPage->show(
                500,
                'Erreur',
                'Impossible de retirer cet utilisateur du groupe.',
                '/admin/groups/members?id_group=' . $idGroup,
                'Retour aux membres'
            );
            return;
        }
        /*
        * Régénération du token CSRF
        */
        $this->csrf->regenerate();
        /*
        * Retour à la liste des membres
        */
        header(
            'Location: /admin/groups/members?id_group='
            . $idGroup
        );
        exit;
    }
}