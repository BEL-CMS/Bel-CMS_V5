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
use BelCMS\Core\BDD;
if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

final class Model
{
    public function getNews(): array
    {
        $sql = new BDD;
        $sql->table('TABLE_NEWS');
        $sql->fields([
            'id','rewrite_name','name','date_create','author','content',
            'tags','cat','view','img','like_post'
        ]);
        $sql->orderby([
            ['name' => 'date_create', 'type' => 'DESC']
        ]);
        $sql->limit(10);
        $sql->queryAll();
        return $sql->data;
    }

    public function getNewsPage(int $page, int $limit): array
    {
        $offset = max(0, ($page - 1) * $limit);
        $sql = new BDD;
        $sql->table('TABLE_NEWS');
        $sql->fields([
            'id','rewrite_name','name','date_create','author','content',
            'tags','cat','view','img','like_post'
        ]);
        $sql->orderby([
            ['name' => 'date_create', 'type' => 'DESC']
        ]);
        $sql->limit([$offset, $limit], true);
        $sql->queryAll();
        return $sql->data;
    }

    public function getNewsCount(): int
    {
        $sql = new BDD;
        $sql->table('TABLE_NEWS');
        $sql->count();
        return (int) $sql->data;
    }

    public function getNewsById(int $id): mixed
    {
        $sql = new BDD;
        $sql->table('TABLE_NEWS');
        $sql->where(['name' => 'id', 'value' => $id]);
        $sql->queryOne();
        return $sql->data;
    }

    public function getNewsByRewrite(string $rewrite): mixed
    {
        $sql = new BDD;
        $sql->table('TABLE_NEWS');
        $sql->where(['name' => 'rewrite_name', 'value' => $rewrite]);
        $sql->queryOne();
        return $sql->data;
    }

    public function getCategories(): array
    {
        $sql = new BDD;
        $sql->table('TABLE_NEWS_CAT');
        $sql->orderby([
            ['name' => 'id', 'type' => 'ASC']
        ]);
        $sql->queryAll();
        return $sql->data;
    }

    public function getNewsByCategory(int $category): array
    {
        $sql = new BDD;
        $sql->table('TABLE_NEWS');
        $sql->where(['name' => 'cat', 'value' => $category]);
        $sql->orderby([
            ['name' => 'date_create', 'type' => 'DESC']
        ]);
        $sql->queryAll();
        return $sql->data;
    }

    public function addView(int $id): bool
    {
        $article = $this->getNewsById($id);
        if (!$article) {
            return false;
        }
        $view = (int) $article->view + 1;
        $sql = new BDD;
        $sql->table('TABLE_NEWS');
        $sql->where(['name' => 'id', 'value' => $id]);
        return $sql->update(['view' => $view]);
    }
}
