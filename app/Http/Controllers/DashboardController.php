<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Insumo;
use App\Models\User;
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
        $empleadosTotal = auth()->user()->esAdministrador()
            ? User::where('cargo', 'Empleado')->count()
            : null;

        $ventasHoy = Pedido::whereDate('created_at', today())
            ->where('estado', 'Completado')
            ->sum('total');

        $stockCritico = Insumo::whereColumn('stock_actual', '<=', 'stock_minimo')->count();

        $stockBajo = Insumo::whereColumn('stock_actual', '>', 'stock_minimo')
            ->whereRaw('stock_actual <= stock_minimo * 1.2')
            ->count();

        $produccionHoy = DB::table('detalles_pedidos')
            ->join('pedidos', 'detalles_pedidos.pedido_id', '=', 'pedidos.id')
            ->whereDate('pedidos.fecha_entrega', today())
            ->sum('detalles_pedidos.cantidad');

        $pedidosSemana = Pedido::whereBetween('fecha_pedido', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();

        // Pedidos recientes
        $pedidosRecientes = Pedido::with(['cliente', 'detalles.producto'])
            ->latest()
            ->take(5)
            ->get();

        // Productos más vendidos, según las unidades de pedidos completados.
        $productosMasVendidos = DB::table('productos')
            ->join('detalles_pedidos', 'productos.id', '=', 'detalles_pedidos.producto_id')
            ->join('pedidos', 'detalles_pedidos.pedido_id', '=', 'pedidos.id')
            ->selectRaw('productos.nombre, SUM(detalles_pedidos.cantidad) as cantidad')
            ->where('pedidos.estado', 'Completado')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get();
        return view('dashboard', compact(
            'pedidosPendientes',
            'pedidosEnProceso',
            'pedidosHoy',
            'clientesTotal',
            'empleadosTotal',
            'ventasHoy',
            'stockCritico',
            'stockBajo',
            'produccionHoy',
            'pedidosSemana',
            'pedidosRecientes',
            'productosMasVendidos',
        ));
    }
}
