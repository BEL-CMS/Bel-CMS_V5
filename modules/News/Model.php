<?php

declare(strict_types=1);

namespace BelCMS\Modules\News;

use BelCMS\Core\BDD;

final class Model
{
    public function __construct(
        private BDD $db
    ) {
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
            [
                'name' => 'id',
                'type' => 'ASC',
            ],
        ]);

        return $this->db->queryAll();
    }


    /**
     * Récupère une catégorie grâce à son id_cat.
     */
    public function getCategoryByIdCat(
        int $idCat
    ): ?object {
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

        return $result ?: null;
    }


    /**
     * Compte les actualités publiées.
     */
    public function countPublishedNews(): int
    {
        $this->db->table('TABLE_NEWS');

        $this->db->where([
            [
                'name'  => 'status',
                'value' => 1,
            ],
        ]);

        $this->db->count();

        return (int) $this->db->data;
    }


    /**
     * Récupère les actualités publiées.
     */
    public function getPublishedNews(
        int $limit = 10,
        int $offset = 0
    ): array {
        $limit = max(1, $limit);
        $offset = max(0, $offset);

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
                'name'  => 'status',
                'value' => 1,
            ],
        ]);

        $this->db->orderby([
            [
                'name' => 'created_at',
                'type' => 'DESC',
            ],
        ]);

        $this->db->limit([
            'start' => $offset,
            'length' => $limit,
        ]);

        return $this->db->queryAll();
    }


    /**
     * Récupère les actualités publiées d'une catégorie.
     */
    public function getPublishedNewsByCategory(
        int $idCat,
        int $limit = 10,
        int $offset = 0
    ): array {
        if ($idCat <= 0) {
            return [];
        }

        $limit = max(1, $limit);
        $offset = max(0, $offset);

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
                'name'  => 'status',
                'value' => 1,
            ],
            [
                'name'  => 'id_cat',
                'value' => $idCat,
            ],
        ]);

        $this->db->orderby([
            [
                'name' => 'created_at',
                'type' => 'DESC',
            ],
        ]);

        $this->db->limit([
            'start' => $offset,
            'length' => $limit,
        ]);

        return $this->db->queryAll();
    }


    /**
     * Récupère une actualité grâce à son ID.
     */
    public function getNewsById(
        int $id
    ): ?object {
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

        return $result ?: null;
    }


    /**
     * Récupère une actualité grâce à son slug.
     */
    public function getNewsBySlug(
        string $slug
    ): ?object {
        $slug = trim($slug);

        if ($slug === '') {
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
                'name'  => 'slug',
                'value' => $slug,
            ],
        ]);

        $result = $this->db->queryOne();

        return $result ?: null;
    }


    /**
     * Vérifie si un slug existe déjà.
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

        $this->db->fields([
            'id',
        ]);

        $conditions = [
            [
                'name'  => 'slug',
                'value' => $slug,
            ],
        ];

        if (
            $excludeId !== null
            && $excludeId > 0
        ) {
            $conditions[] = [
                'name'  => 'id',
                'value' => $excludeId,
                'op'    => '!=',
            ];
        }

        $this->db->where($conditions);

        return $this->db->queryOne() !== false;
    }


    /**
     * Crée une actualité.
     */
    public function createNews(
        array $data
    ): bool {
        if (
            empty($data['hash_key'])
            || empty($data['title'])
            || empty($data['slug'])
            || empty($data['content'])
        ) {
            return false;
        }

        $this->db->table('TABLE_NEWS');

        return $this->db->insert([
            'hash_key'   => (string) $data['hash_key'],
            'id_cat'     => (int) ($data['id_cat'] ?? 0),
            'title'      => (string) $data['title'],
            'slug'       => (string) $data['slug'],
            'image'      => $data['image'] ?? null,
            'content'    => (string) $data['content'],
            'tags'       => $data['tags'] ?? null,
            'views'      => (int) ($data['views'] ?? 0),
            'likes'      => (int) ($data['likes'] ?? 0),
            'status'     => (int) ($data['status'] ?? 0),
            'created_at' => $data['created_at']
                ?? date('Y-m-d H:i:s'),
            'updated_at' => $data['updated_at'] ?? null,
        ]);
    }


    /**
     * Modifie une actualité.
     */
    public function updateNews(
        int $id,
        array $data
    ): bool {
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

        $update = [];

        $allowedFields = [
            'hash_key',
            'id_cat',
            'title',
            'slug',
            'image',
            'content',
            'tags',
            'status',
            'updated_at',
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $update[$field] = $data[$field];
            }
        }

        if ($update === []) {
            return false;
        }

        if (!array_key_exists('updated_at', $update)) {
            $update['updated_at'] = date(
                'Y-m-d H:i:s'
            );
        }

        return $this->db->update($update);
    }


    /**
     * Supprime une actualité.
     */
    public function deleteNews(
        int $id
    ): bool {
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
     * Incrémente le nombre de vues.
     */
    public function incrementViews(
        int $id
    ): bool {
        if ($id <= 0) {
            return false;
        }

        $news = $this->getNewsById($id);

        if ($news === null) {
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
            'views' => (int) $news->views + 1,
        ]);
    }


    /**
     * Incrémente le nombre de likes.
     */
    public function incrementLikes(
        int $id
    ): bool {
        if ($id <= 0) {
            return false;
        }

        $news = $this->getNewsById($id);

        if ($news === null) {
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
            'likes' => (int) $news->likes + 1,
        ]);
    }
}