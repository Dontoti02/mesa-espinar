<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Rol extends Model
{
    protected string $table = 'roles';
    protected array $fillable = ['nombre', 'slug', 'descripcion', 'activo'];

    public function getPermisosIds(int $rolId): array
    {
        $stmt = $this->db()->prepare("SELECT permiso_id FROM roles_permisos WHERE rol_id = :rol_id");
        $stmt->execute([':rol_id' => $rolId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function sincronizarPermisos(int $rolId, array $permisosIds): void
    {
        $this->beginTransaction();
        try {
            // Eliminar anteriores
            $stmt = $this->db()->prepare("DELETE FROM roles_permisos WHERE rol_id = :rol_id");
            $stmt->execute([':rol_id' => $rolId]);

            // Insertar nuevos
            if (!empty($permisosIds)) {
                $insertStmt = $this->db()->prepare("INSERT INTO roles_permisos (rol_id, permiso_id) VALUES (:rol_id, :permiso_id)");
                foreach ($permisosIds as $permisoId) {
                    $insertStmt->execute([
                        ':rol_id' => $rolId,
                        ':permiso_id' => (int)$permisoId
                    ]);
                }
            }

            $this->commit();
        } catch (\Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
