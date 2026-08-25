<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio_venta',
        'costo_produccion',
        'stock_disponible',
        'stock_minimo',
        'tiempo_preparacion_dias',
        'unidad_medida',
        'estado',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class)
            ->withPivot('cantidad_necesaria')
            ->withTimestamps();
    }

    public function detallesPedido()
    {
        return $this->hasMany(DetallePedido::class);
    }
}
