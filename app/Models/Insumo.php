<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad',
        'stock_actual',
        'stock_minimo',
        'precio_unitario',
        'proveedor',
        'estado',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class)
            ->withPivot('cantidad_necesaria')
            ->withTimestamps();
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
