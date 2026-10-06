<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInsumo;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        [$desde, $hasta] = $this->fechas($request);

        $pedidosPeriodo = Pedido::query()->whereBetween('fecha_pedido', [$desde, $hasta]);
        $ventas = (clone $pedidosPeriodo)->where('estado', 'Completado');
        $cantidadVentas = (clone $ventas)->count();
        $totalVentas = (float) (clone $ventas)->sum('total');
        $ticketPromedio = $cantidadVentas ? $totalVentas / $cantidadVentas : 0;

        $estados = (clone $pedidosPeriodo)
            ->selectRaw('estado, COUNT(*) as cantidad')
            ->groupBy('estado')
            ->pluck('cantidad', 'estado');

        $productos = DB::table('detalles_pedidos')
            ->join('pedidos', 'pedidos.id', '=', 'detalles_pedidos.pedido_id')
            ->join('productos', 'productos.id', '=', 'detalles_pedidos.producto_id')
            ->where('pedidos.estado', 'Completado')
            ->whereBetween('pedidos.fecha_pedido', [$desde, $hasta])
            ->selectRaw('productos.nombre, SUM(detalles_pedidos.cantidad) as unidades, SUM(detalles_pedidos.subtotal) as subtotal')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('unidades')
            ->limit(5)
            ->get();

        $ventasPorDia = (clone $ventas)
            ->selectRaw('fecha_pedido, SUM(total) as total')
            ->groupBy('fecha_pedido')
            ->orderBy('fecha_pedido')
            ->get();

        $salidasManuales = MovimientoInsumo::with('insumo')
            ->where('tipo', 'Salida')
            ->whereNull('pedido_id')
            ->whereDate('created_at', '>=', $desde)
            ->whereDate('created_at', '<=', $hasta)
            ->latest()
            ->limit(10)
            ->get();

        return view('reportes.index', compact(
            'desde', 'hasta', 'cantidadVentas', 'totalVentas', 'ticketPromedio',
            'estados', 'productos', 'ventasPorDia', 'salidasManuales',
        ));
    }

    public function exportarVentas(Request $request): StreamedResponse
    {
        [$desde, $hasta] = $this->fechas($request);
        $nombre = "ventas_{$desde}_{$hasta}.csv";

        return response()->streamDownload(function () use ($desde, $hasta) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, ['Pedido', 'Fecha del pedido', 'Cliente', 'Productos', 'Total (Bs)', 'Estado'], ';');

            Pedido::with(['cliente', 'detalles.producto'])
                ->where('estado', 'Completado')
                ->whereBetween('fecha_pedido', [$desde, $hasta])
                ->orderBy('id')
                ->chunkById(200, function ($pedidos) use ($salida) {
                    foreach ($pedidos as $pedido) {
                        $productos = $pedido->detalles
                            ->map(fn ($detalle) => ($detalle->producto?->nombre ?? 'Producto eliminado').' x'.$detalle->cantidad)
                            ->implode(', ');
                        fputcsv($salida, [
                            $pedido->codigo_pedido,
                            $pedido->fecha_pedido->format('Y-m-d'),
                            $this->textoCsv($pedido->cliente?->nombre_completo ?? 'Cliente eliminado'),
                            $this->textoCsv($productos),
                            number_format((float) $pedido->total, 2, ',', ''),
                            $pedido->estado,
                        ], ';');
                    }
                });

            fclose($salida);
        }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'no-store']);
    }

    private function fechas(Request $request): array
    {
        $datos = $request->validate([
            'desde' => 'nullable|date_format:Y-m-d',
            'hasta' => 'nullable|date_format:Y-m-d|after_or_equal:desde',
        ]);

        $desde = $datos['desde'] ?? today()->startOfMonth()->toDateString();
        $hasta = $datos['hasta'] ?? today()->toDateString();
        if ($hasta < $desde) {
            throw ValidationException::withMessages(['hasta' => 'La fecha final debe ser igual o posterior a la inicial.']);
        }

        return [$desde, $hasta];
    }

    private function textoCsv(string $valor): string
    {
        return preg_match('/^[\s\x00-\x1F]*[=+\-@]/u', $valor) ? "'".$valor : $valor;
    }
}
