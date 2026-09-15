<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MovimientoInsumo extends Model
{
    protected $table = 'movimientos_insumo';

    protected $fillable = [
        'insumo_id',
        'pedido_id',
        'usuario_id',
        'tipo',
        'cantidad',
        'stock_anterior',
        'stock_posterior',
        'motivo',
        'movimiento_origen_id',
        'revertido_at',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'stock_anterior' => 'decimal:2',
        'stock_posterior' => 'decimal:2',
        'revertido_at' => 'datetime',
    ];

    public function lotes()
    {
        return $this->belongsToMany(LoteInsumo::class, 'lote_movimiento', 'movimiento_insumo_id', 'lote_insumo_id')->withPivot('cantidad');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function movimientoOrigen()
    {
        return $this->belongsTo(self::class, 'movimiento_origen_id');
    }

    public static function registrar(
        Insumo $insumo,
        string $tipo,
        float $cantidad,
        string $motivo,
        ?int $usuarioId = null,
        ?int $pedidoId = null,
        ?int $movimientoOrigenId = null,
        ?float $stockAjustado = null,
        ?string $codigoLote = null,
        ?string $fechaVencimiento = null,
        ?int $loteId = null,
    ): self {
        return DB::transaction(function () use ($insumo, $tipo, $cantidad, $motivo, $usuarioId, $pedidoId, $movimientoOrigenId, $stockAjustado, $codigoLote, $fechaVencimiento, $loteId) {
            $insumo = Insumo::whereKey($insumo->id)->lockForUpdate()->firstOrFail();
            $stockAnterior = (float) $insumo->stock_actual;
            $cantidad = round($cantidad, 2);

            if ($cantidad < 0) {
                throw new InvalidArgumentException('La cantidad del movimiento no puede ser negativa.');
            }

            if ($tipo === 'Entrada') {
                $stockPosterior = $stockAnterior + $cantidad;
            } elseif ($tipo === 'Salida') {
                if ($stockAnterior < $cantidad) {
                    throw new InvalidArgumentException('No hay suficiente stock para registrar esta salida.');
                }

                $stockPosterior = $stockAnterior - $cantidad;
            } elseif ($tipo === 'Ajuste') {
                if ($stockAjustado === null || $stockAjustado < 0) {
                    throw new InvalidArgumentException('El nuevo stock del ajuste no es valido.');
                }

                $stockPosterior = round($stockAjustado, 2);
                $cantidad = abs($stockPosterior - $stockAnterior);
            } else {
                throw new InvalidArgumentException('El tipo de movimiento no es valido.');
            }

            $insumo->stock_actual = round($stockPosterior, 2);
            $insumo->actualizarEstado();

            $movimiento = self::create([
                'insumo_id' => $insumo->id,
                'pedido_id' => $pedidoId,
                'usuario_id' => $usuarioId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_posterior' => $stockPosterior,
                'motivo' => $motivo,
                'movimiento_origen_id' => $movimientoOrigenId,
            ]);
            GestorLotes::aplicar($movimiento, $codigoLote, $fechaVencimiento, $loteId);

            return $movimiento;
        });
    }
}
