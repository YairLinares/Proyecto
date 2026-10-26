<?php

namespace App\Providers;

use App\Models\Insumo;
use App\Models\LoteInsumo;
use App\Models\Pedido;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // El túnel puede reenviar Host: localhost. Usar la URL pública configurada
        // para formularios, recursos y redirecciones mientras esté habilitada.
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            $view->with('notificaciones', $this->obtenerNotificaciones());
        });
    }

    private function obtenerNotificaciones()
    {
        $lotesPorVencer = auth()->user()?->esAdministrador()
            ? LoteInsumo::with('insumo')
                ->where('cantidad', '>', 0)
                ->whereDate('fecha_vencimiento', '>=', today())
                ->whereDate('fecha_vencimiento', '<=', today()->addDays(7))
                ->orderBy('fecha_vencimiento')
                ->take(5)
                ->get()
                ->map(fn ($lote) => [
                    'tipo' => 'vencimiento',
                    'icono' => 'fa-clock',
                    'color' => '#b77900',
                    'titulo' => "Por vencer: {$lote->insumo->nombre} (lote {$lote->codigo})",
                    'detalle' => 'Vence el '.$lote->fecha_vencimiento->format('d/m/Y').' · '.number_format((float) $lote->cantidad, 2, ',', '.').' '.$lote->insumo->unidad.' disponibles.',
                    'url' => route('insumos.show', $lote->insumo),
                    'fecha' => $lote->fecha_vencimiento,
                    'fecha_texto' => $lote->fecha_vencimiento->isToday() ? 'Vence hoy' : 'Vence '.$lote->fecha_vencimiento->diffForHumans(),
                ])
            : collect();

        $lotesVencidos = auth()->user()?->esAdministrador()
            ? LoteInsumo::with('insumo')
                ->where('cantidad', '>', 0)
                ->whereDate('fecha_vencimiento', '<', today())
                ->orderByDesc('fecha_vencimiento')
                ->take(3)
                ->get()
                ->map(fn ($lote) => [
                    'tipo' => 'vencido',
                    'icono' => 'fa-triangle-exclamation',
                    'color' => '#dc3545',
                    'titulo' => "Lote vencido: {$lote->insumo->nombre} ({$lote->codigo})",
                    'detalle' => 'Venció el '.$lote->fecha_vencimiento->format('d/m/Y').' · '.number_format((float) $lote->cantidad, 2, ',', '.').' '.$lote->insumo->unidad.' por retirar.',
                    'url' => route('insumos.show', $lote->insumo),
                    'fecha' => $lote->fecha_vencimiento,
                    'fecha_texto' => 'Vencido',
                ])
            : collect();

        $stockCritico = auth()->user()?->esAdministrador()
            ? Insumo::whereColumn('stock_actual', '<=', 'stock_minimo')
                ->latest('updated_at')
                ->take(5)
                ->get()
                ->map(fn ($insumo) => [
                    'tipo' => 'stock',
                    'icono' => 'fa-circle',
                    'color' => '#dc3545',
                    'titulo' => "Stock critico: {$insumo->nombre} ({$insumo->stock_actual} {$insumo->unidad})",
                    'detalle' => 'Revisa el inventario para reponer este insumo.',
                    'url' => route('insumos.show', $insumo),
                    'fecha' => $insumo->updated_at,
                ])
            : collect();

        $pedidos = Pedido::with(['cliente', 'detalles.producto'])
            ->latest('updated_at')
            ->take(8)
            ->get()
            ->map(function ($pedido) {
                $estado = match ($pedido->estado) {
                    'Pendiente' => [
                        'tipo' => 'pendiente',
                        'icono' => 'fa-hourglass-half',
                        'color' => '#d4a300',
                        'texto' => 'esta pendiente',
                    ],
                    'En proceso' => [
                        'tipo' => 'proceso',
                        'icono' => 'fa-kitchen-set',
                        'color' => '#0d6efd',
                        'texto' => 'esta en proceso',
                    ],
                    'Completado' => [
                        'tipo' => 'completado',
                        'icono' => 'fa-check-circle',
                        'color' => '#198754',
                        'texto' => 'esta completado',
                    ],
                    default => [
                        'tipo' => 'cancelado',
                        'icono' => 'fa-circle-xmark',
                        'color' => '#dc3545',
                        'texto' => 'esta cancelado',
                    ],
                };

                $productos = $pedido->detalles
                    ->pluck('producto.nombre')
                    ->filter()
                    ->implode(', ');

                return [
                    'tipo' => $estado['tipo'],
                    'icono' => $estado['icono'],
                    'color' => $estado['color'],
                    'titulo' => "Pedido {$pedido->codigo_pedido} de {$pedido->cliente->nombre_completo} {$estado['texto']}",
                    'detalle' => trim(($productos ?: 'Sin productos') . ' | Entrega: ' . $pedido->fecha_entrega->format('d/m/Y') . ' | Total: Bs ' . number_format($pedido->total, 2, ',', '.')),
                    'url' => route('pedidos.show', $pedido),
                    'fecha' => $pedido->updated_at,
                ];
            });

        return $lotesPorVencer
            ->concat($lotesVencidos)
            ->concat($stockCritico)
            ->concat($pedidos)
            ->values();
    }
}
