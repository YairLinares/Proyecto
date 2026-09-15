<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GestorLotes
{
    public static function aplicar(MovimientoInsumo $movimiento, ?string $codigo, ?string $fecha, ?int $loteId): void
    {
        $insumo = $movimiento->insumo;
        $delta = round($movimiento->stock_posterior - $movimiento->stock_anterior, 2);
        if ($delta == 0) {
            return;
        }
        if ($delta > 0) {
            $origen = $movimiento->movimientoOrigen;
            if ($origen && $origen->lotes()->exists()) {
                if ($origen->revertido_at || $origen->insumo_id !== $insumo->id || abs((float) $origen->cantidad - $delta) > 0.001) {
                    self::error('La devolución no coincide con el movimiento original.');
                }
                foreach ($origen->lotes()->lockForUpdate()->get() as $lote) {
                    $lote->increment('cantidad', $lote->pivot->cantidad);
                    $movimiento->lotes()->attach($lote->id, ['cantidad' => $lote->pivot->cantidad]);
                }
                $origen->update(['revertido_at' => now()]);

                return;
            }
            $codigo = $codigo ?: 'AUTO-'.Str::uuid();
            $lote = $insumo->lotes()->where('codigo', $codigo)->lockForUpdate()->first();
            if ($lote && $lote->fecha_vencimiento?->format('Y-m-d') !== $fecha) {
                self::error('Ese lote ya existe con otra fecha de vencimiento.');
            }
            $lote ??= $insumo->lotes()->create(['codigo' => $codigo, 'fecha_vencimiento' => $fecha, 'cantidad' => 0]);
            $lote->increment('cantidad', $delta);
            $movimiento->lotes()->attach($lote->id, ['cantidad' => $delta]);

            return;
        }
        $query = $insumo->lotes()->where('cantidad', '>', 0);
        if ($loteId && ! $movimiento->pedido_id) {
            // Una salida manual seleccionada permite desechar un lote vencido.
            $query->whereKey($loteId);
        } else {
            $query->utilizables();
        }
        $lotes = $query->orderByRaw('fecha_vencimiento IS NULL')->orderBy('fecha_vencimiento')->orderBy('id')->lockForUpdate()->get();
        $pendiente = round(-$delta, 2);
        if (round($lotes->sum('cantidad'), 2) < $pendiente) {
            self::error('No hay cantidad suficiente en los lotes disponibles de '.$insumo->nombre.'. Los lotes vencidos no se pueden consumir.');
        }
        foreach ($lotes as $lote) {
            $cantidad = min($pendiente, (float) $lote->cantidad);
            if ($cantidad <= 0) {
                break;
            }
            $lote->decrement('cantidad', $cantidad);
            $movimiento->lotes()->attach($lote->id, ['cantidad' => $cantidad]);
            $pendiente = round($pendiente - $cantidad, 2);
        }
    }

    private static function error(string $mensaje): never
    {
        throw ValidationException::withMessages(['cantidad' => $mensaje]);
    }
}
