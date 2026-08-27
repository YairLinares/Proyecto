<?php

namespace App\Providers;

use App\Models\Insumo;
use App\Models\Pedido;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $view->with('notificaciones', $this->obtenerNotificaciones());
        });
    }

    private function obtenerNotificaciones()
    {
        $stockCritico = Insumo::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(fn ($insumo) => [
                'tipo' => 'stock',
                'icono' => 'fa-circle',
                'color' => '#dc3545',
                'titulo' => "Stock crítico: {$insumo->nombre} ({$insumo->stock_actual} {$insumo->unidad})",
                'fecha' => $insumo->updated_at,
            ]);

        $nuevosPedidos = Pedido::with('cliente')
            ->latest('created_at')
            ->take(3)
            ->get()
            ->map(fn ($pedido) => [
                'tipo' => 'pedido',
                'icono' => 'fa-birthday-cake',
                'color' => '#c7436f',
                'titulo' => "Nuevo pedido {$pedido->numero_pedido} de {$pedido->cliente->nombre_completo}",
                'fecha' => $pedido->created_at,
            ]);

        $pedidosCompletados = Pedido::where('estado', 'Completado')
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(fn ($pedido) => [
                'tipo' => 'completado',
                'icono' => 'fa-check-circle',
                'color' => '#198754',
                'titulo' => "Pedido {$pedido->numero_pedido} marcado como Completado",
                'fecha' => $pedido->updated_at,
            ]);

        return $stockCritico->concat($nuevosPedidos)
            ->concat($pedidosCompletados)
            ->sortByDesc('fecha')
            ->take(8)
            ->values();
    }
}
