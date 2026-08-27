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
        $ultimoCodigo = self::where('numero_pedido', 'REGEXP', '^PED-[0-9]{3}$')
            ->orderByRaw('CAST(SUBSTRING(numero_pedido, 5) AS UNSIGNED) DESC')
            ->value('numero_pedido');

        $siguiente = $ultimoCodigo ? ((int) substr($ultimoCodigo, 4) + 1) : 1;

        return 'PED-' . str_pad((string) $siguiente, 3, '0', STR_PAD_LEFT);
    }

    public function getCodigoPedidoAttribute(): string
    {
        return $this->numero_pedido ?: 'PED-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT);
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
