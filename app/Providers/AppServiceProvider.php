<?php

namespace App\Providers;

use App\Models\Insumo;
use App\Models\Pedido;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $view->with('notificaciones', $this->obtenerNotificaciones());
        });
    }

    private function obtenerNotificaciones()
    {
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

        return $stockCritico
            ->concat($pedidos)
            ->sortByDesc('fecha')
            ->take(8)
            ->values();
    }
}
