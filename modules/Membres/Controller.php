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

namespace BelCMS\Modules\Membres;

use BelCMS\Core\Language;
use BelCMS\Core\View;

final class Controller
{
    public function __construct(
        private Model $model,
        private View $view,
        private Language $language
    ) {
    }

    /**
     * Liste publique des membres
     */
    public function index(): void
    {
        $members = $this->model->getMembers();

        echo $this->view->render(
            'Membres',
            'index',
            [
                'members'  => $members,
                'language' => $this->language,
            ]
        );
    }

    /**
     * Profil public d'un membre
     */
    public function profile(): void
    {
        $hashKey = trim(
            (string) ($_GET['hash_key'] ?? '')
        );

        /*
         * Aucun hash_key fourni
         */
        if ($hashKey === '') {

            http_response_code(404);

            echo $this->view->render(
                'Membres',
                'profile',
                [
                    'member'   => null,
                    'social'   => null,
                    'language' => $this->language,
                    'notFound' => true,
                ]
            );

            return;
        }

        /*
         * Récupération du membre
         */
        $member = $this->model->getMemberByHashKey($hashKey);

        /*
         * Membre inexistant
         */
        if ($member === null) {

            http_response_code(404);

            echo $this->view->render(
                'Membres',
                'profile',
                [
                    'member'   => null,
                    'social'   => null,
                    'language' => $this->language,
                    'notFound' => true,
                ]
            );

            return;
        }

        /*
         * Réseaux sociaux
         */
        $social = $this->model->getSocialByHashKey($hashKey);

        /*
         * Affichage
         */
        echo $this->view->render(
            'Membres',
            'profile',
            [
                'member'   => $member,
                'social'   => $social,
                'language' => $this->language,
                'notFound' => false,
            ]
        );
    }
}