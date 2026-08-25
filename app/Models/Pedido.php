<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'numero_pedido',
        'cliente_id',
        'usuario_id',
        'tipo_pedido',
        'prioridad',
        'fecha_pedido',
        'fecha_entrega',
        'descripcion_especificaciones',
        'direccion_entrega',
        'telefono_contacto',
        'metodo_pago',
        'subtotal',
        'anticipo_recibido',
        'descuento',
        'costo_envio',
        'total',
        'estado',
    ];

    protected $casts = [
        'fecha_pedido' => 'date',
        'fecha_entrega' => 'date',
    ];

    public static function generarNumeroPedido(): string
    {
        $ultimoPedido = self::latest('id')->first();
        $siguiente = $ultimoPedido ? $ultimoPedido->id + 1 : 1;

        return 'PED-' . now()->format('Ymd') . '-' . str_pad((string) $siguiente, 4, '0', STR_PAD_LEFT);
    }

    public function calcularTotal(): void
    {
        $this->total = max(0, $this->subtotal + $this->costo_envio - $this->descuento);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
