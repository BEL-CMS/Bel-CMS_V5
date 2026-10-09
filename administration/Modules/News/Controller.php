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

namespace BelCMS\Admin\News;

use BelCMS\Core\Assets;
use BelCMS\Core\Csrf;
use BelCMS\Core\Errors\ErrorPage;
use BelCMS\Core\View;
use BelCMS\Core\User;

final class Controller
{
    private View $view;
    private Assets $assets;
    private Model $model;
    private Csrf $csrf;
    private ErrorPage $errorPage;
    private User $user;
    public function __construct(
        View $view,
        Assets $assets,
        Model $model,
        Csrf $csrf,
        ErrorPage $errorPage,
        User $user
    ) {
        $this->view = $view;
        $this->assets = $assets;
        $this->model = $model;
        $this->csrf = $csrf;
        $this->errorPage = $errorPage;
        $this->user = $user;
    }
    /**
     * Liste des actualités.
     */
    public function index(): void
    {
        $this->assets->css(
            '/administration/Modules/News/css/news.css'
        );
        $news = $this->model->getNews();
        $categories = $this->model->getCategories();
        echo $this->view->renderAdmin(
            'News',
            'index',
            [
                'news'       => $news,
                'categories' => $categories,
            ]
        );
    }
    /**
     * Création d'une actualité.
     */
    public function create(): void
    {
        $this->assets->css(
            '/administration/Modules/News/css/news.css'
        );
        $this->assets->js(
            '/assets/plugins/tinymce/tinymce.min.js'
        );
        $this->assets->js(
            '/assets/plugins/tinymce/run.js'
        );
        $categories = $this->model->getCategories();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !$this->csrf->verify(
                    $_POST['csrf_token'] ?? null
                )
            ) {
                $this->errorPage->show(
                    403,
                    'Requête invalide',
                    'Le jeton de sécurité est invalide ou a expiré.'
                );
                return;
            }
            $title = trim(
                (string) ($_POST['title'] ?? '')
            );
            $slug = trim(
                (string) ($_POST['slug'] ?? '')
            );
            $idCat = (int) (
                $_POST['id_cat'] ?? 0
            );
            $image = trim(
                (string) ($_POST['image'] ?? '')
            );
            $content = trim(
                (string) ($_POST['content'] ?? '')
            );
            $tags = trim(
                (string) ($_POST['tags'] ?? '')
            );
            $status = (int) (
                $_POST['status'] ?? 0
            );
            $hashKey = $this->user->hashKey();
            if ($hashKey === null || $hashKey === '') {
                $this->errorPage->show(
                    400,
                    'Auteur invalide',
                    'Impossible de déterminer l’auteur de cette actualité.'
                );
                return;
            }
            if (
                $title === ''
                || $slug === ''
                || $idCat <= 0
                || $content === ''
            ) {
                echo $this->view->renderAdmin(
                    'News',
                    'create',
                    [
                        'categories' => $categories,
                        'error'      => 'Veuillez remplir tous les champs obligatoires.',
                        'form'       => $_POST,
                    ]
                );
                return;
            }
            if ($this->model->slugExists($slug)) {
                echo $this->view->renderAdmin(
                    'News',
                    'create',
                    [
                        'categories' => $categories,
                        'error'      => 'Ce slug est déjà utilisé.',
                        'form'       => $_POST,
                    ]
                );
                return;
            }
            $created = $this->model->createNews(
                $hashKey,
                $idCat,
                $title,
                $slug,
                $image !== '' ? $image : null,
                $content,
                $tags !== '' ? $tags : null,
                $status === 1 ? 1 : 0
            );
            if (!$created) {
                echo $this->view->renderAdmin(
                    'News',
                    'create',
                    [
                        'csrf'       => $this->csrf->field(),
                        'categories' => $categories,
                        'error'      => 'Impossible de créer cette actualité.',
                        'form'       => $_POST,
                    ]
                );
                return;
            }
            header(
                'Location: /admin/news'
            );
            exit;
        }
        echo $this->view->renderAdmin(
            'News',
            'create',
            [
                'csrf'       => $this->csrf->field(),
                'categories' => $categories,
                'form'       => [],
            ]
        );
    }
    /**
     * Modification d'une actualité.
     */
    public function edit(): void
    {
        $this->assets->css(
            '/administration/Modules/News/css/news.css'
        );
        $id = (int) (
            $_GET['id'] ?? $_POST['id'] ?? 0
        );
        if ($id <= 0) {
            $this->errorPage->show(
                404,
                'Actualité introuvable',
                'L’identifiant de l’actualité est invalide.'
            );
            return;
        }
        $news = $this->model->getNewsById($id);
        if ($news === null) {
            $this->errorPage->show(
                404,
                'Actualité introuvable',
                'Cette actualité n’existe pas.'
            );
            return;
        }
        $categories = $this->model->getCategories();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !$this->csrf->verify(
                    $_POST['csrf_token'] ?? null
                )
            ) {
                $this->errorPage->show(
                    403,
                    'Requête invalide',
                    'Le jeton de sécurité est invalide ou a expiré.'
                );
                return;
            }
            $title = trim(
                (string) ($_POST['title'] ?? '')
            );
            $slug = trim(
                (string) ($_POST['slug'] ?? '')
            );
            $idCat = (int) (
                $_POST['id_cat'] ?? 0
            );
            $image = trim(
                (string) ($_POST['image'] ?? '')
            );
            $content = trim(
                (string) ($_POST['content'] ?? '')
            );
            $tags = trim(
                (string) ($_POST['tags'] ?? '')
            );
            $status = (int) (
                $_POST['status'] ?? 0
            );
            if (
                $title === ''
                || $slug === ''
                || $idCat <= 0
                || $content === ''
            ) {
                echo $this->view->renderAdmin(
                    'News',
                    'edit',
                    [
                        'news'       => $news,
                        'categories' => $categories,
                        'error'      => 'Veuillez remplir tous les champs obligatoires.',
                        'form'       => $_POST,
                        'csrf' => $this->csrf->field(),
                    ]
                );
                return;
            }
            if (
                $this->model->slugExists(
                    $slug,
                    $id
                )
            ) {
                echo $this->view->renderAdmin(
                    'News',
                    'edit',
                    [
                        'news'       => $news,
                        'categories' => $categories,
                        'error'      => 'Ce slug est déjà utilisé.',
                        'form'       => $_POST,
                    ]
                );
                return;
            }
            $updated = $this->model->updateNews(
                $id,
                $idCat,
                $title,
                $slug,
                $image !== '' ? $image : null,
                $content,
                $tags !== '' ? $tags : null,
                $status === 1 ? 1 : 0
            );
            if (!$updated) {
                echo $this->view->renderAdmin(
                    'News',
                    'edit',
                    [
                        'news'       => $news,
                        'categories' => $categories,
                        'error'      => 'Impossible de modifier cette actualité.',
                        'form'       => $_POST,
                    ]
                );
                return;
            }
            header(
                'Location: /admin/news'
            );
            exit;
        }
        echo $this->view->renderAdmin(
            'News',
            'edit',
            [
                'news'       => $news,
                'categories' => $categories,
                'form'       => [],
                'csrf' => $this->csrf->field(),
            ]
        );
    }
    /**
     * Suppression d'une actualité.
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->errorPage->show(
                405,
                'Méthode non autorisée',
                'Cette action doit être effectuée en POST.'
            );
            return;
        }
        if (
            !$this->csrf->verify(
                $_POST['csrf_token'] ?? null
            )
        ) {
            $this->errorPage->show(
                403,
                'Requête invalide',
                'Le jeton de sécurité est invalide ou a expiré.'
            );
            return;
        }
        $id = (int) (
            $_POST['id'] ?? 0
        );
        if ($id <= 0) {
            $this->errorPage->show(
                400,
                'Identifiant invalide',
                'L’actualité sélectionnée est invalide.'
            );
            return;
        }
        if (!$this->model->deleteNews($id)) {
            $this->errorPage->show(
                500,
                'Suppression impossible',
                'Impossible de supprimer cette actualité.'
            );
            return;
        }
        header(
            'Location: /admin/news'
        );
        exit;
    }
    /**
     * Publication / dépublication.
     */
    public function status(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->errorPage->show(
                405,
                'Méthode non autorisée',
                'Cette action doit être effectuée en POST.'
            );
            return;
        }
        if (
            !$this->csrf->verify(
                $_POST['csrf_token'] ?? null
            )
        ) {
            $this->errorPage->show(
                403,
                'Requête invalide',
                'Le jeton de sécurité est invalide ou a expiré.'
            );
            return;
        }
        $id = (int) (
            $_POST['id'] ?? 0
        );
        $status = (int) (
            $_POST['status'] ?? 0
        );
        if ($id <= 0) {
            $this->errorPage->show(
                400,
                'Identifiant invalide',
                'L’actualité sélectionnée est invalide.'
            );
            return;
        }
        if (!$this->model->updateStatus($id, $status)) {
            $this->errorPage->show(
                500,
                'Modification impossible',
                'Impossible de modifier le statut de cette actualité.'
            );
            return;
        }
        header(
            'Location: /admin/news'
        );
        exit;
    }
    /**
     * Gestion des catégories.
     */
    public function categories(): void
    {
        $this->assets->css(
            '/administration/Modules/News/css/news.css'
        );
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !$this->csrf->verify(
                    $_POST['csrf_token'] ?? null
                )
            ) {
                $this->errorPage->show(
                    403,
                    'Requête invalide',
                    'Le jeton de sécurité est invalide ou a expiré.'
                );
                return;
            }
            $action = trim(
                (string) ($_POST['action'] ?? '')
            );
            if ($action === 'create') {
                $name = trim(
                    (string) ($_POST['name'] ?? '')
                );
                $description = trim(
                    (string) ($_POST['description'] ?? '')
                );
                $color = trim(
                    (string) ($_POST['color'] ?? '#2381c0')
                );
                if (
                    !$this->model->createCategory(
                        $name,
                        $description !== '' ? $description : null,
                        $color
                    )
                ) {
                    echo $this->view->renderAdmin(
                        'News',
                        'categories',
                        [
                            'categories' => $this->model->getCategories(),
                            'error'      => 'Impossible de créer cette catégorie.',
                            'form'       => $_POST,
                        ]
                    );
                    return;
                }
            }
            if ($action === 'edit') {
                $id = (int) (
                    $_POST['id'] ?? 0
                );
                $name = trim(
                    (string) ($_POST['name'] ?? '')
                );
                $description = trim(
                    (string) ($_POST['description'] ?? '')
                );
                $color = trim(
                    (string) ($_POST['color'] ?? '#2381c0')
                );
                if (
                    !$this->model->updateCategory(
                        $id,
                        $name,
                        $description !== '' ? $description : null,
                        $color
                    )
                ) {
                    echo $this->view->renderAdmin(
                        'News',
                        'categories',
                        [
                            'categories' => $this->model->getCategories(),
                            'error'      => 'Impossible de modifier cette catégorie.',
                            'form'       => $_POST,
                        ]
                    );
                    return;
                }
            }
            if ($action === 'delete') {
                $id = (int) (
                    $_POST['id'] ?? 0
                );
                if (!$this->model->deleteCategory($id)) {
                    echo $this->view->renderAdmin(
                        'News',
                        'categories',
                        [
                            'categories' => $this->model->getCategories(),
                            'error'      => 'Cette catégorie ne peut pas être supprimée. Vérifiez qu’elle ne contient aucune actualité.',
                            'form'       => [],
                        ]
                    );
                    return;
                }
            }
            header(
                'Location: /admin/news/categories'
            );
            exit;
        }
        echo $this->view->renderAdmin(
            'News',
            'categories',
            [
                'categories' => $this->model->getCategories(),
                'form'       => [],
            ]
        );
    }
}