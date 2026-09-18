<?php

namespace App\Models;

use App\Core\Model;

class Oficina extends Model
{
    protected string $table = 'oficinas';
    protected array $fillable = [
        'nombre', 'sigla', 'responsable', 'correo', 'telefono', 'orden', 'activo'
    ];

    public function allActivas(string $orderBy = 'orden ASC, nombre ASC'): array
    {
        return $this->where('activo = 1', [], $orderBy);
    }
}
