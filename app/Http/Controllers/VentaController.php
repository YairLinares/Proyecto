<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'desde' => 'nullable|date_format:Y-m-d',
            'hasta' => 'nullable|date_format:Y-m-d'.($request->filled('desde') ? '|after_or_equal:desde' : ''),
            'buscar' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
        ]);
        $desde = $request->get('desde');
        $hasta = $request->get('hasta');
        $buscar = $request->get('buscar');

        $query = Pedido::query()
            ->with(['cliente', 'detalles.producto'])
            ->where('estado', 'Completado');

        if ($desde) {
            $query->whereDate('fecha_pedido', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('fecha_pedido', '<=', $hasta);
        }

        if ($buscar) {
            $query->where(function ($subquery) use ($buscar) {
                $subquery->where('numero_pedido', 'like', "%{$buscar}%")
                    ->orWhereHas('cliente', function ($clientes) use ($buscar) {
                        $clientes->where('nombre_completo', 'like', "%{$buscar}%");
                    });
            });
        }

        $totalVentas = (clone $query)->sum('total');
        $cantidadVentas = (clone $query)->count();
        $ticketPromedio = $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0;
        $ventas = $query->latest('fecha_pedido')->paginate(12)->withQueryString();

        return view('ventas.index', compact(
            'ventas',
            'desde',
            'hasta',
            'buscar',
            'totalVentas',
            'cantidadVentas',
            'ticketPromedio',
        ));
    }
}
