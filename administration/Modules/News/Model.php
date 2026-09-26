<?php
/**
 * Bel-CMS [Content management system]
 * @version 5.0.0 [PHP8.5]
 * @link https://bel-cms.dev
 * @license Apache-2.0 license
 * @copyright 2015-2026 Bel-CMS
 */
declare(strict_types=1);

namespace BelCMS\Admin\News;
use BelCMS\Core\BDD;
final class Model
{
    private BDD $db;
    public function __construct(BDD $db)
    {
        $this->db = $db;
    }
    /**
     * Récupère toutes les actualités pour l'administration.
     */
    public function getNews(): array
    {
        $this->db->table('TABLE_NEWS');
        $this->db->fields([
            'id',
            'hash_key',
            'id_cat',
            'title',
            'slug',
            'image',
            'tags',
            'views',
            'likes',
            'status',
            'created_at',
            'updated_at',
        ]);
        $this->db->orderby([
            'created_at' => 'DESC',
        ]);
        return $this->db->queryAll();
    }
    /**
     * Récupère une actualité par son ID technique.
     */
    public function getNewsById(int $id): ?object
    {
        if ($id <= 0) {
            return null;
        }
        $this->db->table('TABLE_NEWS');
        $this->db->fields([
            'id',
            'hash_key',
            'id_cat',
            'title',
            'slug',
            'image',
            'content',
            'tags',
            'views',
            'likes',
            'status',
            'created_at',
            'updated_at',
        ]);
        $this->db->where([
            [
                'name'  => 'id',
                'value' => $id,
            ],
        ]);
        $result = $this->db->queryOne();
        return $result !== false ? $result : null;
    }
    /**
     * Récupère toutes les catégories.
     */
    public function getCategories(): array
    {
        $this->db->table('TABLE_NEWS_CAT');
        $this->db->fields([
            'id',
            'id_cat',
            'name',
            'description',
            'color',
        ]);
        $this->db->orderby([
            'id_cat' => 'ASC',
        ]);
        return $this->db->queryAll();
    }
    /**
     * Récupère une catégorie par son id_cat.
     */
    public function getCategoryByIdCat(int $idCat): ?object
    {
        if ($idCat <= 0) {
            return null;
        }
        $this->db->table('TABLE_NEWS_CAT');
        $this->db->fields([
            'id',
            'id_cat',
            'name',
            'description',
            'color',
        ]);
        $this->db->where([
            [
                'name'  => 'id_cat',
                'value' => $idCat,
            ],
        ]);
        $result = $this->db->queryOne();
        return $result !== false ? $result : null;
    }
    /**
     * Vérifie si un slug existe.
     */
    public function slugExists(
        string $slug,
        ?int $excludeId = null
    ): bool {
        $slug = trim($slug);
        if ($slug === '') {
            return false;
        }
        $this->db->table('TABLE_NEWS');
        $this->db->where([
            [
                'name'  => 'slug',
                'value' => $slug,
            ],
        ]);
        if ($excludeId !== null && $excludeId > 0) {
            $this->db->where([
                [
                    'name'  => 'id',
                    'op'    => '!=',
                    'value' => $excludeId,
                ],
            ]);
        }
        return $this->db->count() > 0;
    }
    /**
     * Vérifie si une catégorie possède déjà ce nom.
     */
    public function categoryNameExists(
        string $name,
        ?int $excludeId = null
    ): bool {
        $name = trim($name);
        if ($name === '') {
            return false;
        }
        $this->db->table('TABLE_NEWS_CAT');
        $this->db->where([
            [
                'name'  => 'name',
                'value' => $name,
            ],
        ]);
        if ($excludeId !== null && $excludeId > 0) {
            $this->db->where([
                [
                    'name'  => 'id',
                    'op'    => '!=',
                    'value' => $excludeId,
                ],
            ]);
        }
        return $this->db->count() > 0;
    }
    /**
     * Retourne le prochain id_cat disponible.
     */
    public function getNextCategoryId(): int
    {
        $this->db->table('TABLE_NEWS_CAT');
        $this->db->fields([
            'MAX(id_cat) AS max_id_cat',
        ]);
        $result = $this->db->queryOne();
        $max = (int) ($result->max_id_cat ?? 0);
        return max(1, $max + 1);
    }
    /**
     * Crée une actualité.
     */
    public function createNews(
        string $hashKey,
        int $idCat,
        string $title,
        string $slug,
        ?string $image,
        string $content,
        ?string $tags,
        int $status
    ): bool {
        $title = trim($title);
        $slug = trim($slug);
        if (
            $hashKey === ''
            || $idCat <= 0
            || $title === ''
            || $slug === ''
            || trim($content) === ''
        ) {
            return false;
        }
        if ($this->slugExists($slug)) {
            return false;
        }
        if ($this->getCategoryByIdCat($idCat) === null) {
            return false;
        }
        $this->db->table('TABLE_NEWS');
        return $this->db->insert([
            'hash_key'   => $hashKey,
            'id_cat'     => $idCat,
            'title'      => $title,
            'slug'       => $slug,
            'image'      => $image,
            'content'    => $content,
            'tags'       => $tags,
            'views'      => 0,
            'likes'      => 0,
            'status'     => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null,
        ]);
    }
    /**
     * Modifie une actualité.
     */
    public function updateNews(
        int $id,
        int $idCat,
        string $title,
        string $slug,
        ?string $image,
        string $content,
        ?string $tags,
        int $status
    ): bool {
        if ($id <= 0) {
            return false;
        }
        $title = trim($title);
        $slug = trim($slug);
        if (
            $idCat <= 0
            || $title === ''
            || $slug === ''
            || trim($content) === ''
        ) {
            return false;
        }
        if ($this->getNewsById($id) === null) {
            return false;
        }
        if ($this->slugExists($slug, $id)) {
            return false;
        }
        if ($this->getCategoryByIdCat($idCat) === null) {
            return false;
        }
        $this->db->table('TABLE_NEWS');
        $this->db->where([
            [
                'name'  => 'id',
                'value' => $id,
            ],
        ]);
        return $this->db->update([
            'id_cat'     => $idCat,
            'title'      => $title,
            'slug'       => $slug,
            'image'      => $image,
            'content'    => $content,
            'tags'       => $tags,
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
    /**
     * Supprime une actualité.
     */
    public function deleteNews(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }
        $this->db->table('TABLE_NEWS');
        $this->db->where([
            [
                'name'  => 'id',
                'value' => $id,
            ],
        ]);
        return $this->db->delete();
    }
    /**
     * Modifie le statut d'une actualité.
     */
    public function updateStatus(int $id, int $status): bool
    {
        if ($id <= 0) {
            return false;
        }
        $status = $status === 1 ? 1 : 0;
        $this->db->table('TABLE_NEWS');
        $this->db->where([
            [
                'name'  => 'id',
                'value' => $id,
            ],
        ]);
        return $this->db->update([
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
    /**
     * Crée une catégorie.
     */
    public function createCategory(
        string $name,
        ?string $description,
        string $color
    ): bool {
        $name = trim($name);
        $color = trim($color);
        if ($name === '' || $color === '') {
            return false;
        }
        if ($this->categoryNameExists($name)) {
            return false;
        }
        $idCat = $this->getNextCategoryId();
        $this->db->table('TABLE_NEWS_CAT');
        return $this->db->insert([
            'id_cat'      => $idCat,
            'name'        => $name,
            'description' => $description,
            'color'       => $color,
        ]);
    }
    /**
     * Modifie une catégorie.
     */
    public function updateCategory(
        int $id,
        string $name,
        ?string $description,
        string $color
    ): bool {
        if ($id <= 0) {
            return false;
        }
        $name = trim($name);
        $color = trim($color);
        if ($name === '' || $color === '') {
            return false;
        }
        if ($this->categoryNameExists($name, $id)) {
            return false;
        }
        $category = $this->getCategoryById($id);
        if ($category === null) {
            return false;
        }
        $this->db->table('TABLE_NEWS_CAT');
        $this->db->where([
            [
                'name'  => 'id',
                'value' => $id,
            ],
        ]);
        return $this->db->update([
            'name'        => $name,
            'description' => $description,
            'color'       => $color,
        ]);
    }
    /**
     * Récupère une catégorie par son ID technique.
     */
    public function getCategoryById(int $id): ?object
    {
        if ($id <= 0) {
            return null;
        }
        $this->db->table('TABLE_NEWS_CAT');
        $this->db->fields([
            'id',
            'id_cat',
            'name',
            'description',
            'color',
        ]);
        $this->db->where([
            [
                'name'  => 'id',
                'value' => $id,
            ],
        ]);
        $result = $this->db->queryOne();
        return $result !== false ? $result : null;
    }
    /**
     * Vérifie si une catégorie contient des actualités.
     */
    public function countNewsByCategory(int $idCat): int
    {
        if ($idCat <= 0) {
            return 0;
        }
        $this->db->table('TABLE_NEWS');
        $this->db->where([
            [
                'name'  => 'id_cat',
                'value' => $idCat,
            ],
        ]);
        return $this->db->count();
    }
    /**
     * Supprime une catégorie.
     *
     * Une catégorie contenant des actualités ne peut pas être supprimée.
     */
    public function deleteCategory(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }
        $category = $this->getCategoryById($id);
        if ($category === null) {
            return false;
        }
        if ($this->countNewsByCategory((int) $category->id_cat) > 0) {
            return false;
        }
        $this->db->table('TABLE_NEWS_CAT');
        $this->db->where([
            [
                'name'  => 'id',
                'value' => $id,
            ],
        ]);
        return $this->db->delete();
    }
}