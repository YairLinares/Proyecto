<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Insumo;
use App\Models\LoteInsumo;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard con estadísticas
     */
    public function index()
    {
        // Estadísticas generales
        $pedidosPendientes = Pedido::where('estado', 'Pendiente')->count();
        $pedidosEnProceso = Pedido::where('estado', 'En proceso')->count();

        // Tarjetas rápidas del día
        $pedidosHoy = Pedido::whereDate('created_at', today())->count();

        $clientesTotal = Cliente::count();

        $ventasHoy = Pedido::whereDate('created_at', today())
            ->where('estado', 'Completado')
            ->sum('total');

        $stockCritico = Insumo::whereColumn('stock_actual', '<=', 'stock_minimo')->count();

        $stockBajo = Insumo::whereColumn('stock_actual', '>', 'stock_minimo')
            ->whereRaw('stock_actual <= stock_minimo * 1.2')
            ->count();

        $lotesPorVencer = auth()->user()->esAdministrador()
            ? LoteInsumo::with('insumo')
                ->where('cantidad', '>', 0)
                ->whereDate('fecha_vencimiento', '>=', today())
                ->whereDate('fecha_vencimiento', '<=', today()->addDays(7))
                ->orderBy('fecha_vencimiento')
                ->orderBy('id')
                ->get()
            : collect();

        $produccionHoy = DB::table('detalles_pedidos')
            ->join('pedidos', 'detalles_pedidos.pedido_id', '=', 'pedidos.id')
            ->whereDate('pedidos.fecha_entrega', today())
            ->sum('detalles_pedidos.cantidad');

        $pedidosSemana = Pedido::whereBetween('fecha_pedido', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();

        // Entregas del mes actual y pedidos atrasados que siguen activos.
        $inicioMes = today()->startOfMonth()->toDateString();
        $inicioMesSiguiente = today()->startOfMonth()->addMonth()->toDateString();
        $pedidosRecientes = Pedido::with(['cliente', 'detalles.producto'])
            ->where(function ($query) use ($inicioMes, $inicioMesSiguiente) {
                $query->where(function ($mesActual) use ($inicioMes, $inicioMesSiguiente) {
                    $mesActual->where('fecha_entrega', '>=', $inicioMes)
                        ->where('fecha_entrega', '<', $inicioMesSiguiente);
                })->orWhere(function ($atrasados) use ($inicioMes) {
                    $atrasados->where('fecha_entrega', '<', $inicioMes)
                        ->whereIn('estado', ['Pendiente', 'En proceso']);
                });
            })
            ->latest()
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // Producto líder por unidades vendidas en todo el historial completado.
        $productoMasVendido = DB::table('productos')
            ->join('detalles_pedidos', 'productos.id', '=', 'detalles_pedidos.producto_id')
            ->join('pedidos', 'detalles_pedidos.pedido_id', '=', 'pedidos.id')
            ->selectRaw('productos.id, productos.nombre, productos.imagen, SUM(detalles_pedidos.cantidad) as cantidad')
            ->where('pedidos.estado', 'Completado')
            ->groupBy('productos.id', 'productos.nombre', 'productos.imagen')
            ->orderByDesc('cantidad')
            ->orderBy('productos.id')
            ->first();
        return view('dashboard', compact(
            'pedidosPendientes',
            'pedidosEnProceso',
            'pedidosHoy',
            'clientesTotal',
            'ventasHoy',
            'stockCritico',
            'stockBajo',
            'lotesPorVencer',
            'produccionHoy',
            'pedidosSemana',
            'pedidosRecientes',
            'productoMasVendido',
        ));
    }
}
