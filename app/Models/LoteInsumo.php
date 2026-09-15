<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteInsumo extends Model
{
    protected $table = 'lotes_insumo';

    protected $fillable = ['insumo_id', 'codigo', 'fecha_vencimiento', 'cantidad'];

    protected $casts = ['fecha_vencimiento' => 'date', 'cantidad' => 'decimal:2'];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    public function scopeUtilizables($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('fecha_vencimiento')->orWhereDate('fecha_vencimiento', '>=', today());
        });
    }
}
