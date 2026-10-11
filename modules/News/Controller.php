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

namespace BelCMS\Modules\News;

use BelCMS\Core\Assets;
use BelCMS\Core\View;

final class Controller
{
    public function __construct(
        private View $view,
        private Assets $assets,
        private Model $model
    ) {
    }

    /**
     * Liste des actualités.
     */
    public function index(): void
    {
        $this->loadAssets();

        $news = $this->model->getPublishedNews(10, 0);
        $categories = $this->model->getCategories();

        echo $this->view->render(
            'News',
            'index',
            [
                'news' => $news,
                'categories' => $categories,
            ]
        );
    }

    /**
     * Affichage d'une actualité.
     */
    public function view(): void
    {
        $this->loadAssets();

        $slug = trim((string) ($_GET['slug'] ?? ''));

        if ($slug === '') {
            $this->renderNotFound();
            return;
        }

        $news = $this->model->getNewsBySlug($slug);

        if ($news === null || (int) ($news->status ?? 0) !== 1) {
            $this->renderNotFound();
            return;
        }

        $category = null;
        $idCat = (int) ($news->id_cat ?? 0);

        if ($idCat > 0) {
            $category = $this->model->getCategoryByIdCat($idCat);
        }

        if ($this->model->incrementViews((int) $news->id)) {
            $news->views = (int) ($news->views ?? 0) + 1;
        }

        echo $this->view->render(
            'News',
            'show',
            [
                'news' => $news,
                'category' => $category,
            ]
        );
    }

    /**
     * Affichage des actualités d'une catégorie.
     */
    public function category(): void
    {
        $this->loadAssets();

        $idCat = filter_input(
            INPUT_GET,
            'id_cat',
            FILTER_VALIDATE_INT
        );

        if ($idCat === false || $idCat === null || $idCat <= 0) {
            $this->renderCategoryNotFound();
            return;
        }

        $category = $this->model->getCategoryByIdCat($idCat);

        if ($category === null) {
            $this->renderCategoryNotFound();
            return;
        }

        $news = $this->model->getPublishedNewsByCategory(
            $idCat,
            10,
            0
        );

        echo $this->view->render(
            'News',
            'category',
            [
                'category' => $category,
                'news' => $news,
            ]
        );
    }

    /**
     * Ajoute un like à une actualité.
     */
    public function like(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            return;
        }

        $id = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        if ($id === false || $id === null || $id <= 0) {
            http_response_code(400);
            return;
        }

        if (!$this->model->incrementLikes($id)) {
            http_response_code(404);
            return;
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/news'));
        exit;
    }

    private function loadAssets(): void
    {
        $this->assets->css('/modules/News/css/news.css');
        $this->assets->js('/modules/News/js/news.js');
    }

    private function renderNotFound(): void
    {
        http_response_code(404);

        echo $this->view->render(
            'News',
            'show',
            [
                'news' => null,
                'category' => null,
            ]
        );
    }

    private function renderCategoryNotFound(): void
    {
        http_response_code(404);

        echo $this->view->render(
            'News',
            'category',
            [
                'category' => null,
                'news' => [],
            ]
        );
    }
}
