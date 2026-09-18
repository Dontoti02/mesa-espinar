<?php

namespace App\Models;

use App\Core\Model;

class Estado extends Model
{
    protected string $table = 'estados_expediente';
    protected array $fillable = [
        'codigo', 'nombre', 'color', 'icono', 'orden', 'es_publico', 'activo'
    ];

    public function findByCodigo(string $codigo): ?array
    {
        return $this->first("codigo = :c", [':c' => $codigo]);
    }

    public function allActivos(string $orderBy = 'orden ASC, id ASC'): array
    {
        return $this->where('activo = 1', [], $orderBy);
    }
}
