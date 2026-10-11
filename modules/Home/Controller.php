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
namespace BelCMS\Modules\Home;
use BelCMS\Core\Assets;
use BelCMS\Core\View;
use BelCMS\Modules\News\Model as NewsModel;
final class Controller
{
    public function __construct(
        private View $view,
        private Assets $assets,
        private Model $model,
        private NewsModel $newsModel
    ) {
    }
    public function index(): void
    {
        /*
         * -------------------------------------------------
         * Assets
         * -------------------------------------------------
         */
        $this->assets->moduleCss('Home');
        /*
         * -------------------------------------------------
         * Dernières actualités
         * -------------------------------------------------
         */
        $news = $this->newsModel->getPublishedNews(3, 0);
        /*
         * -------------------------------------------------
         * Statistiques
         * -------------------------------------------------
         */
        $stats = $this->model->getStatistics();
        /*
         * -------------------------------------------------
         * Vue
         * -------------------------------------------------
         */
        echo $this->view->render(
            'Home',
            'index',
            [
                'title' => 'Accueil',
                'news'  => $news,
                'stats' => $stats,
            ]
        );
    }
}