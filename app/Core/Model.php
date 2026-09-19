<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    protected function db(): PDO
    {
        return Database::getConnection();
    }

    public function beginTransaction(): bool
    {
        return $this->db()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->db()->commit();
    }

    public function rollBack(): bool
    {
        if ($this->db()->inTransaction()) {
            return $this->db()->rollBack();
        }
        return false;
    }

    public function find(int|string $id): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    protected function sanitizeOrderBy(string $orderBy): string
    {
        $allowedDirections = ['ASC', 'DESC'];
        $parts = explode(',', $orderBy);
        $safe = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if (preg_match('/^(\w+)\s+(ASC|DESC)$/i', $part, $m)) {
                $safe[] = "`{$m[1]}` {$m[2]}";
            } elseif (preg_match('/^(\w+)$/i', $part, $m)) {
                $safe[] = "`{$m[1]}` ASC";
            }
        }

        return !empty($safe) ? implode(', ', $safe) : "`id` DESC";
    }

    public function all(string $orderBy = 'id DESC'): array
    {
        $safeOrderBy = $this->sanitizeOrderBy($orderBy);
        $stmt = $this->db()->query("SELECT * FROM `{$this->table}` ORDER BY {$safeOrderBy}");
        return $stmt->fetchAll();
    }

    public function where(string $condition, array $params = [], string $orderBy = 'id DESC', ?int $limit = null): array
    {
        $safeOrderBy = $this->sanitizeOrderBy($orderBy);
        $safeLimit = max(1, (int)$limit);
        $sql = "SELECT * FROM `{$this->table}` WHERE {$condition} ORDER BY {$safeOrderBy}";
        if ($limit !== null) {
            $sql .= " LIMIT {$safeLimit}";
        }
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function first(string $condition, array $params = []): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM `{$this->table}` WHERE {$condition} LIMIT 1");
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function insert(array $data): int|string
    {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ":{$f}", $fields);

        $sql = sprintf(
            "INSERT INTO `%s` (`%s`) VALUES (%s)",
            $this->table,
            implode('`, `', $fields),
            implode(', ', $placeholders)
        );

        $stmt = $this->db()->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->execute();

        return $this->db()->lastInsertId();
    }

    public function update(int|string $id, array $data): bool
    {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        $fields = array_map(fn($f) => "`{$f}` = :{$f}", array_keys($data));
        $sql = sprintf(
            "UPDATE `%s` SET %s WHERE `%s` = :primary_id",
            $this->table,
            implode(', ', $fields),
            $this->primaryKey
        );

        $stmt = $this->db()->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':primary_id', $id);

        return $stmt->execute();
    }

    public function delete(int|string $id): bool
    {
        $stmt = $this->db()->prepare("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id");
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function count(string $condition = '1=1', array $params = []): int
    {
        $stmt = $this->db()->prepare("SELECT COUNT(*) FROM `{$this->table}` WHERE {$condition}");
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function paginate(int $page = 1, int $perPage = 20, string $condition = '1=1', array $params = [], string $orderBy = 'id DESC'): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $totalCount = $this->count($condition, $params);
        $totalPages = (int)ceil($totalCount / $perPage);

        $safeOrderBy = $this->sanitizeOrderBy($orderBy);
        $safePerPage = max(1, (int)$perPage);
        $safeOffset = max(0, (int)$offset);
        $sql = "SELECT * FROM `{$this->table}` WHERE {$condition} ORDER BY {$safeOrderBy} LIMIT {$safePerPage} OFFSET {$safeOffset}";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_records' => $totalCount,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
    }
}
