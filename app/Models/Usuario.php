<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Usuario extends Model
{
    protected string $table = 'usuarios';
    protected array $fillable = [
        'nombres', 'apellidos', 'dni', 'usuario', 'correo',
        'rol_id', 'oficina_id', 'cargo', 'telefono', 'estado'
    ];

    public function findByLogin(string $identifier): ?array
    {
        $sql = "SELECT u.*, r.nombre AS rol_nombre, r.slug AS rol_slug, o.nombre AS oficina_nombre, o.sigla AS oficina_sigla
                FROM `{$this->table}` u
                INNER JOIN roles r ON u.rol_id = r.id
                LEFT JOIN oficinas o ON u.oficina_id = o.id
                WHERE (u.usuario = :id1 OR u.correo = :id2)
                LIMIT 1";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':id1' => $identifier, ':id2' => $identifier]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function resetPassword(int $id, string $hashedPassword): bool
    {
        $sql = "UPDATE `{$this->table}` SET `password` = :pw, `debe_cambiar_password` = 1 WHERE `{$this->primaryKey}` = :id";
        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(':pw', $hashedPassword);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function createUser(array $data, string $hashedPassword, bool $debeCambiar): int|string
    {
        $data['password'] = $hashedPassword;
        $data['debe_cambiar_password'] = $debeCambiar ? 1 : 0;

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

    public function getUsuariosFiltrados(array $filtros = [], int $page = 1, int $perPage = 15): array
    {
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filtros['buscar'])) {
            $conditions[] = "(u.nombres LIKE :b OR u.apellidos LIKE :b OR u.usuario LIKE :b OR u.correo LIKE :b OR u.dni LIKE :b)";
            $params[':b'] = '%' . trim($filtros['buscar']) . '%';
        }

        if (!empty($filtros['rol_id'])) {
            $conditions[] = "u.rol_id = :rol_id";
            $params[':rol_id'] = (int)$filtros['rol_id'];
        }

        if (!empty($filtros['oficina_id'])) {
            $conditions[] = "u.oficina_id = :oficina_id";
            $params[':oficina_id'] = (int)$filtros['oficina_id'];
        }

        if (isset($filtros['estado']) && $filtros['estado'] !== '') {
            $conditions[] = "u.estado = :estado";
            $params[':estado'] = (int)$filtros['estado'];
        }

        $whereClause = implode(' AND ', $conditions);

        // Contar total
        $countSql = "SELECT COUNT(*) FROM `{$this->table}` u WHERE {$whereClause}";
        $stmt = $this->db()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $sql = "SELECT u.*, r.nombre AS rol_nombre, r.slug AS rol_slug, o.nombre AS oficina_nombre, o.sigla AS oficina_sigla
                FROM `{$this->table}` u
                INNER JOIN roles r ON u.rol_id = r.id
                LEFT JOIN oficinas o ON u.oficina_id = o.id
                WHERE {$whereClause}
                ORDER BY u.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_records' => $total,
            'total_pages' => (int)ceil($total / $perPage)
        ];
    }
}
