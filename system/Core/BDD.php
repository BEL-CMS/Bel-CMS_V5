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
namespace BelCMS\Core;

if (!defined('CHECK_INDEX')):
	header($_SERVER['SERVER_PROTOCOL'] . ' 403 Direct access forbidden');
	exit('<!doctype html><html><head><meta charset="utf-8"><title>BEL-CMS : Error 403 Forbidden</title><style>h1{margin: 20px auto;text-align:center;color: red;}p{text-align:center;font-weight:bold;</style></head><body><h1>HTTP Error 403 : Forbidden</h1><p>You don\'t permission to access / on this server.</p></body></html>');
endif;

use PDO;
use PDOException;
use Generator;
use RuntimeException;

class BDD
{
    private PDO $db;
    private string $table   = '';
    private string $fields  = '*';
    private string $where   =  '';
    private string $orderby = '';
    private string $limit   = '';
    private string $join    = '';

    public mixed $data      = null;
    public int $rowCount    = 0;
    public int|string|null $lastId = null;
    public string $update   = '';

    private bool $isObject    = true;
    private array $sqlInsert  = [];
    private array $parameters = [];
    private string $likeValue = '';

    public function __construct()
    {
        $this->db = PDOConnection::getInstance()->getConnection();
    }

    public function table(?string $data = null): self
    {
        if ($data === null || trim($data) === '') {
            return $this;
        }

        $tmp = preg_split('/\s+/', trim($data));
        $table = $tmp[0];

        if (defined($table)) {
            $table = constant($table);
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new RuntimeException('Nom de table invalide : ' . $table);
        }

        if (isset($tmp[1])) {
            $alias = $tmp[1];
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $alias)) {
                throw new RuntimeException('Alias de table invalide : ' . $alias);
            }
            $this->table = $table . ' ' . $alias;
        } else {
            $this->table = $table;
        }

        return $this;
    }

    public function fields(array $data = []): self
    {
        if (empty($data)) {
            $this->fields = '*';
            return $this;
        }

        $fields = [];
        foreach ($data as $value) {
            if (is_string($value)) {
                $fields[] = $value;
            }
        }

        $this->fields = $fields ? implode(',', $fields) : '*';
        return $this;
    }

    public function limit(int|array|false $data, bool $custom = false): self
    {
        if ($data === false || $data === 0) {
            $this->limit = '';
            return $this;
        }

        if ($custom && is_array($data)) {
            $this->limit = ' LIMIT ' . intval($data[0]) . ',' . intval($data[1]);
            return $this;
        }

        $this->limit = ' LIMIT ' . intval($data);
        return $this;
    }

    public function orderby(mixed $data = false, bool $custom = false): self
    {
        if ($data === false || $data === null || $data === '') {
            $this->orderby = '';
            return $this;
        }

        if ($custom) {
            $this->orderby = ' ORDER BY ' . $data;
            return $this;
        }

        if (is_array($data)) {
            $tmp = [];
            foreach ($data as $value) {
                if (!is_array($value) || !isset($value['name'], $value['type'])) {
                    continue;
                }
                $direction = strtoupper((string) $value['type']);
                if (!in_array($direction, ['ASC', 'DESC'], true)) {
                    $direction = 'ASC';
                }
                $tmp[] = $value['name'] . ' ' . $direction;
            }
            if ($tmp) {
                $this->orderby = ' ORDER BY ' . implode(',', $tmp);
            }
            return $this;
        }

        $direction = strtoupper((string) $data);
        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'ASC';
        }
        $this->orderby = ' ORDER BY id ' . $direction;
        return $this;
    }

    public function where(array|string|false $data = []): self
    {
        $this->parameters = [];
        $this->likeValue = '';

        if ($data === false) {
            $this->where = '';
            return $this;
        }

        if (is_string($data)) {
            $this->where = ' ' . $data;
            return $this;
        }

        $conditions = [];
        $index = 0;

        if (isset($data['name']) && array_key_exists('value', $data)) {
            $conditions[] = $this->buildCondition($data, $index);
        } else {
            foreach ($data as $condition) {
                if (!is_array($condition)) {
                    continue;
                }
                if (!isset($condition['name']) || !array_key_exists('value', $condition)) {
                    continue;
                }
                $conditions[] = $this->buildCondition($condition, $index++);
            }
        }

        $this->where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        return $this;
    }

    public function whereLike(array $data): self
    {
        if (!isset($data['name']) || !array_key_exists('value', $data)) {
            return $this;
        }
        $this->where = ' WHERE ' . $data['name'] . ' LIKE :like';
        $this->parameters = [];
        $this->likeValue = '%' . $data['value'] . '%';
        return $this;
    }

    public function whereLikeRight(array $data): self
    {
        if (!isset($data['name']) || !array_key_exists('value', $data)) {
            return $this;
        }
        $this->where = ' WHERE ' . $data['name'] . ' LIKE :like';
        $this->parameters = [];
        $this->likeValue = $data['value'] . '%';
        return $this;
    }

    public function whereLikeLeft(array $data): self
    {
        if (!isset($data['name']) || !array_key_exists('value', $data)) {
            return $this;
        }
        $this->where = ' WHERE ' . $data['name'] . ' LIKE :like';
        $this->parameters = [];
        $this->likeValue = '%' . $data['value'];
        return $this;
    }

    public function join(?array $data = null): self
    {
        $this->join = '';
        if (empty($data)) {
            return $this;
        }

        foreach ($data as $value) {
            if (!is_array($value)) {
                continue;
            }
            $table = $value['table'] ?? '';
            $type = strtoupper($value['type'] ?? 'INNER');
            $on = $value['on'] ?? '';
            if ($table === '' || $on === '') {
                continue;
            }
            if (defined($table)) {
                $table = constant($table);
            }
            if (!in_array($type, ['INNER', 'LEFT', 'RIGHT', 'FULL'], true)) {
                $type = 'INNER';
            }
            $this->join .= ' ' . $type . ' JOIN ' . $table . ' ON ' . $on;
        }
        return $this;
    }

    public function isObject(bool $data = true): self
    {
        $this->isObject = $data;
        return $this;
    }

    public function queryOne(): mixed
    {
        $stmt = $this->prepareAndExecute($this->buildSelect(), $this->getParameters());
        $this->data = $stmt->fetch($this->isObject ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC);
        $this->rowCount = $this->data ? 1 : 0;
        $stmt->closeCursor();
        return $this->data;
    }

    public function queryAll(): array
    {
        $stmt = $this->prepareAndExecute($this->buildSelect(), $this->getParameters());
        $this->data = $stmt->fetchAll($this->isObject ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC);
        $this->rowCount = count($this->data);
        $stmt->closeCursor();
        return $this->data;
    }

    public function queryLarge(): Generator
    {
        $stmt = $this->db->prepare($this->buildSelect());
        $this->bindParameters($stmt, $this->getParameters());
        $stmt->execute();
        $stmt->setFetchMode($this->isObject ? PDO::FETCH_OBJ : PDO::FETCH_ASSOC);
        while ($row = $stmt->fetch()) {
            yield $row;
        }
        $stmt->closeCursor();
    }

    public function count(): int
    {
        if ($this->table === '') {
            throw new RuntimeException('Aucune table définie.');
        }
        $sql = 'SELECT COUNT(*) FROM ' . $this->table . $this->join . $this->where;
        $stmt = $this->prepareAndExecute($sql, $this->getParameters());
        $this->data = (int) $stmt->fetchColumn();
        $stmt->closeCursor();
        return $this->data;
    }

    public function insert(?array $data = null): bool
    {
        if (empty($data) || $this->table === '') {
            $this->data = false;
            return false;
        }

        $fields = array_keys($data);
        $placeholders = array_map(static fn(string $field): string => ':insert_' . $field, $fields);
        $parameters = [];

        foreach ($data as $field => $value) {
            $parameters[':insert_' . $field] = $value;
        }

        $sql = 'INSERT INTO ' . $this->table
            . ' (`' . implode('`,`', $fields) . '`)' 
            . ' VALUES (' . implode(',', $placeholders) . ')';

        $stmt = $this->prepareAndExecute($sql, $parameters);
        $this->rowCount = $stmt->rowCount();
        $this->lastId = $this->db->lastInsertId();
        $this->data = true;
        $stmt->closeCursor();
        return true;
    }

    public function update(array $data = []): bool
    {
        if (empty($data) || $this->table === '') {
            $this->data = false;
            return false;
        }

        $update = [];
        $parameters = [];

        foreach ($data as $key => $value) {
            $placeholder = ':update_' . $key;
            $update[] = '`' . $key . '` = ' . $placeholder;
            $parameters[$placeholder] = $value;
        }

        foreach ($this->getParameters() as $key => $value) {
            $parameters[$key] = $value;
        }

        $prepare = 'UPDATE ' . $this->table
            . ' SET ' . implode(', ', $update)
            . $this->where;

        $this->update = $prepare;
        $stmt = $this->prepareAndExecute($prepare, $parameters);
        $this->rowCount = $stmt->rowCount();
        $this->data = true;
        $stmt->closeCursor();
        return true;
    }

    public function delete(): bool
    {
        if ($this->table === '') {
            $this->data = false;
            return false;
        }

        $stmt = $this->prepareAndExecute(
            'DELETE FROM ' . $this->table . $this->where,
            $this->getParameters()
        );
        $this->rowCount = $stmt->rowCount();
        $this->data = true;
        $stmt->closeCursor();
        return true;
    }

    public function lastId(): string
    {
        return $this->db->lastInsertId();
    }

    public static function tableExists(string $table): bool
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            return false;
        }
        if (defined($table)) {
            $table = constant($table);
        }
        $db = PDOConnection::getInstance()->getConnection();
        $stmt = $db->prepare('SHOW TABLES LIKE :table');
        $stmt->execute([':table' => $table]);
        return $stmt->fetchColumn() !== false;
    }

    public static function showColumns(string $table): array|false
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            return false;
        }
        if (defined($table)) {
            $table = constant($table);
        }
        $db = PDOConnection::getInstance()->getConnection();
        $stmt = $db->prepare('SHOW COLUMNS FROM `' . $table . '`');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    private function buildSelect(): string
    {
        if ($this->table === '') {
            throw new RuntimeException('Aucune table définie.');
        }

        return 'SELECT ' . $this->fields
            . ' FROM ' . $this->table
            . $this->join
            . $this->where
            . $this->orderby
            . $this->limit;
    }

    private function prepareAndExecute(string $sql, array $parameters = []): \PDOStatement
    {
        try {
            $stmt = $this->db->prepare($sql);
            $this->bindParameters($stmt, $parameters);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Erreur SQL : ' . $e->getMessage() . PHP_EOL . 'Requête : ' . $sql,
                (int) $e->getCode(),
                $e
            );
        }
    }

    private function bindParameters(\PDOStatement $stmt, array $parameters): void
    {
        foreach ($parameters as $key => $value) {
            $placeholder = str_starts_with((string) $key, ':') ? (string) $key : ':' . $key;
            $stmt->bindValue($placeholder, $value, $this->getPDOType($value));
        }
    }

    private function buildCondition(array $condition, int $index): string
    {
        $name = (string) $condition['name'];
        $operator = strtoupper(trim((string) ($condition['op'] ?? '=')));
        $allowed = ['=', '!=', '<>', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE'];
        if (!in_array($operator, $allowed, true)) {
            $operator = '=';
        }

        $placeholder = ':where_' . $index;
        $this->parameters[$placeholder] = $condition['value'];
        return $name . ' ' . $operator . ' ' . $placeholder;
    }

    private function getParameters(): array
    {
        $parameters = $this->parameters;
        if (str_contains($this->where, ':like') && $this->likeValue !== '') {
            $parameters[':like'] = $this->likeValue;
        }
        return $parameters;
    }

    private function getPDOType(mixed $value): int
    {
        return match (true) {
            is_int($value)  => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            is_null($value) => PDO::PARAM_NULL,
            default         => PDO::PARAM_STR,
        };
    }
}
