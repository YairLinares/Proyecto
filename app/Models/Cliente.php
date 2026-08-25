<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre_completo',
        'tipo_documento',
        'numero_documento',
        'telefono_principal',
        'telefono_alternativo',
        'email',
        'ciudad',
        'direccion',
        'tipo_cliente',
        'notas_preferencias',
        'estado',
        'total_compras',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
