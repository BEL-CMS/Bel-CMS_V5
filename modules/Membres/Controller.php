<?php
/** Bel-CMS V5 — Module Membres | Apache-2.0 */
declare(strict_types=1);
namespace BelCMS\Modules\Membres;
use BelCMS\Core\Language;
use BelCMS\Core\View;
final class Controller
{
    public function __construct(private Model $model, private View $view, private Language $language) {}

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim((string) ($_GET['search'] ?? ''));
        $search = mb_substr($search, 0, 100);
        $perPage = 24;
        $total = $this->model->countMembers($search);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);
        echo $this->view->render('Membres', 'index', [
            'members' => $this->model->getMembers($page, $perPage, $search),
            'language' => $this->language, 'page' => $page, 'pages' => $pages,
            'total' => $total, 'search' => $search,
        ]);
    }

    public function profile(): void
    {
        $hashKey = trim((string) ($_GET['hash_key'] ?? ''));
        $member = $hashKey !== '' ? $this->model->getMemberByHashKey($hashKey) : null;
        if ($member === null) http_response_code(404);
        $social = $member !== null ? $this->model->getSocialByHashKey($hashKey) : null;
        echo $this->view->render('Membres', 'profile', [
            'member' => $member, 'social' => $social, 'language' => $this->language,
            'notFound' => $member === null,
        ]);
    }
}
