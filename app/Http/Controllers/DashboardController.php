<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar dashboard con estadísticas
     */
    public function index()
    {
        // Estadísticas generales
        $ventasDelMes = Pedido::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('estado', 'Completado')
            ->sum('total');

        $pedidosActivos = Pedido::whereIn('estado', ['Pendiente', 'En proceso'])->count();

        $clientesNuevos = Cliente::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $alertasStock = Producto::whereRaw('stock_disponible <= stock_minimo')->count();

        // Pedidos recientes
        $pedidosRecientes = Pedido::with('cliente')
            ->latest()
            ->take(5)
            ->get();

        // Datos para gráfico de ventas mensuales
        $ventasMensuales = Pedido::selectRaw('MONTH(created_at) as mes, SUM(total) as total')
            ->where('estado', 'Completado')
            ->whereYear('created_at', now()->year)
            ->groupBy('mes')
            ->get();

        // Datos para gráfico de ventas por categoría
        $ventasPorCategoria = DB::table('productos')
            ->join('detalles_pedidos', 'productos.id', '=', 'detalles_pedidos.producto_id')
            ->join('pedidos', 'detalles_pedidos.pedido_id', '=', 'pedidos.id')
            ->selectRaw('categorias.nombre, COUNT(*) as cantidad')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('pedidos.estado', 'Completado')
            ->groupBy('categorias.nombre')
            ->get();

        $insumosBajos = Insumo::where('estado', '!=', 'Normal')->count();

        return view('dashboard', compact(
            'ventasDelMes',
            'pedidosActivos',
            'clientesNuevos',
            'alertasStock',
            'pedidosRecientes',
            'ventasMensuales',
            'ventasPorCategoria',
            'insumosBajos'
        ));
    }
}