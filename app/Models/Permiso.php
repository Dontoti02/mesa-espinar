<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Permiso extends Model
{
    protected string $table = 'permisos';
    protected array $fillable = ['clave', 'nombre', 'modulo', 'descripcion'];

    public function allAgrupadosPorModulo(): array
    {
        $permisos = $this->all('modulo ASC, id ASC');
        $agrupados = [];
        foreach ($permisos as $p) {
            $agrupados[$p['modulo']][] = $p;
        }
        return $agrupados;
    }
}
