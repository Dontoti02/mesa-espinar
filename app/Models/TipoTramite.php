<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class TipoTramite extends Model
{
    protected string $table = 'tipos_tramite';
    protected array $fillable = [
        'codigo', 'nombre', 'descripcion', 'oficina_sugerida_id',
        'plazo_referencial_dias', 'requisitos', 'instrucciones',
        'permite_virtual', 'requiere_pago', 'monto', 'activo'
    ];

    public function allConOficina(string $where = '1=1', array $params = []): array
    {
        $sql = "SELECT tt.*, o.nombre AS oficina_nombre, o.sigla AS oficina_sigla
                FROM `{$this->table}` tt
                LEFT JOIN oficinas o ON tt.oficina_sugerida_id = o.id
                WHERE {$where}
                ORDER BY tt.nombre ASC";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allActivosVirtuales(): array
    {
        return $this->allConOficina('tt.activo = 1 AND tt.permite_virtual = 1');
    }
}
