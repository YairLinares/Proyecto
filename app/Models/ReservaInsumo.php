<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservaInsumo extends Model
{
    protected $table = 'reservas_insumo';

    protected $fillable = ['pedido_id', 'insumo_id', 'cantidad'];

    protected $casts = ['cantidad' => 'decimal:2'];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }
}
