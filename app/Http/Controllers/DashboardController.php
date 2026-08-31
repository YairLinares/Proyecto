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

        $produccionHoy = DB::table('detalles_pedidos')
            ->join('pedidos', 'detalles_pedidos.pedido_id', '=', 'pedidos.id')
            ->whereDate('pedidos.fecha_entrega', today())
            ->sum('detalles_pedidos.cantidad');

        $pedidosSemana = Pedido::whereBetween('fecha_pedido', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();

        // Producto con stock más crítico
        // Ventas por día de la semana actual (Lun a Dom)
        $inicioSemana = now()->startOfWeek();
        $ventasSemanaRaw = Pedido::where('estado', 'Completado')
            ->whereBetween('fecha_pedido', [$inicioSemana, now()->endOfWeek()])
            ->selectRaw('DAYOFWEEK(fecha_pedido) as dia, SUM(total) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        // DAYOFWEEK: 1=Domingo ... 7=Sábado, reordenamos Lun-Dom
        $ventasSemana = [];
        foreach (range(1, 7) as $i) {
            $diaSql = $i === 7 ? 1 : $i + 1; // Lun(1)->2 ... Dom(7)->1
            $ventasSemana[] = (float) ($ventasSemanaRaw[$diaSql] ?? 0);
        }

        // Pedidos recientes
        $pedidosRecientes = Pedido::with('cliente')
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

        $insumosBajos = Insumo::where('estado', '!=', 'Normal')->count();

        return view('dashboard', compact(
            'ventasDelMes',
            'pedidosActivos',
            'clientesNuevos',
            'pedidosHoy',
            'clientesTotal',
            'ventasHoy',
            'stockCritico',
            'stockBajo',
            'produccionHoy',
            'pedidosSemana',
            'ventasSemana',
            'pedidosRecientes',
            'productosMasVendidos',
            'insumosBajos'
        ));
    }
}
