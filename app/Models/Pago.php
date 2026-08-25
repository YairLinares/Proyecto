<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'pedido_id',
        'monto',
        'metodo_pago',
        'fecha_pago',
        'referencia',
        'observacion',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
