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

use BelCMS\Core\Pagination;
use BelCMS\Core\View;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

final class Controller
{
    private Model $model;
    private View $view;

    public function __construct(Model $model, View $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function index(): void
    {
        $news = $this->model->getNews();

        $categories = $this->model->getCategories();

        foreach ($news as $article) {
            $text = strip_tags($article->content);

            $article->excerpt = mb_strlen($text) > 250
                ? mb_substr($text, 0, 250) . '...'
                : $text;
        }

        echo $this->view->render(
            'News',
            'index',
            [
                'news' => $news,
                'categories' => $categories
            ]
        );
    }

    public function show(string $rewrite): void
    {
        $article = $this->model->getNewsByRewrite($rewrite);

        if (!$article) {
            http_response_code(404);
            echo 'Actualité introuvable';
            return;
        }

        $this->model->addView((int) $article->id);
        $article->view = (int) $article->view + 1;

        echo $this->view->render('News', 'show', [
            'article' => $article,
        ]);
    }

    public function category(string $category): void
    {
        $categories = $this->model->getCategories();
        $categoryId = null;
        $categoryName = null;

        foreach ($categories as $item) {
            if ((string) $item->value === $category) {
                $categoryId = (int) $item->id;
                $categoryName = (string) $item->value;
                break;
            }
        }

        if ($categoryId === null) {
            http_response_code(404);
            echo 'Catégorie introuvable';
            return;
        }

        $news = $this->model->getNewsByCategory($categoryId);

        foreach ($news as $article) {
            $text = trim(strip_tags((string) $article->content));
            $article->excerpt = mb_strlen($text) > 250
                ? mb_substr($text, 0, 250) . '...'
                : $text;
        }

        echo $this->view->render('News', 'category', [
            'news' => $news,
            'categoryName' => $categoryName,
        ]);
    }
}
