<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre_completo',
        'telefono_principal',
        'telefono_alternativo',
        'direccion',
        'tipo_cliente',
        'estado',
        'total_compras',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
