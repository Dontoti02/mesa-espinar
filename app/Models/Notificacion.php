<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Notificacion extends Model
{
    protected string $table = 'notificaciones';
    protected array $fillable = [
        'usuario_id', 'oficina_id', 'titulo', 'mensaje', 'enlace', 'leido', 'fecha_leido'
    ];

    public function getNotificacionesUsuario(int $userId, ?int $oficinaId = null, int $limit = 30): array
    {
        $sql = "SELECT * FROM `{$this->table}` 
                WHERE (usuario_id = :u";
        $params = [':u' => $userId];

        if ($oficinaId) {
            $sql .= " OR oficina_id = :o";
            $params[':o'] = $oficinaId;
        }

        $sql .= ") ORDER BY id DESC LIMIT {$limit}";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarNoLeidas(int $userId, ?int $oficinaId = null): int
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` 
                WHERE (usuario_id = :u";
        $params = [':u' => $userId];

        if ($oficinaId) {
            $sql .= " OR oficina_id = :o";
            $params[':o'] = $oficinaId;
        }

        $sql .= ") AND leido = 0";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function marcarTodasLeidas(int $userId, ?int $oficinaId = null): void
    {
        $sql = "UPDATE `{$this->table}` SET leido = 1, fecha_leido = NOW() 
                WHERE (usuario_id = :u";
        $params = [':u' => $userId];

        if ($oficinaId) {
            $sql .= " OR oficina_id = :o";
            $params[':o'] = $oficinaId;
        }

        $sql .= ") AND leido = 0";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
    }
}
