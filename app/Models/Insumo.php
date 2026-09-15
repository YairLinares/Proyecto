<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected static function booted(): void
    {
        static::created(function (Insumo $insumo) {
            if ($insumo->stock_actual > 0) {
                $insumo->lotes()->create(['codigo' => 'INICIAL-'.$insumo->id, 'cantidad' => $insumo->stock_actual]);
            }
        });
    }

    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad',
        'stock_actual',
        'stock_minimo',
        'precio_unitario',
        'estado',
    ];

    public function lotes()
    {
        return $this->hasMany(LoteInsumo::class);
    }

    public function stockUtilizable(): float
    {
        return (float) $this->lotes()->utilizables()->sum('cantidad');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'insumo_producto')
            ->withPivot('cantidad_necesaria')
            ->withTimestamps();
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInsumo::class);
    }

    public function isStockBajo()
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    public function actualizarEstado(): void
    {
        if ($this->stock_actual <= 0) {
            $this->estado = 'Agotado';
        } elseif ($this->stock_actual <= $this->stock_minimo) {
            $this->estado = 'Stock bajo';
        } else {
            $this->estado = 'Normal';
        }

        $this->save();
    }
}
