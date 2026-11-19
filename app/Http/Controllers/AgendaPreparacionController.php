<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class AgendaPreparacionController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'periodo' => 'nullable|in:todos,atrasados,hoy,proximos,en_proceso',
            'page' => 'nullable|integer|min:1',
        ]);
        $periodo = $validated['periodo'] ?? 'todos';
        $hoy = today();
        $fecha = 'DATE(COALESCE(fecha_preparacion, fecha_entrega))';
        $pendientes = Pedido::where('estado', 'Pendiente');
        $consultas = [
            'atrasados' => (clone $pendientes)->whereRaw("{$fecha} < ?", [$hoy->toDateString()]),
            'hoy' => (clone $pendientes)->whereRaw("{$fecha} = ?", [$hoy->toDateString()]),
            'proximos' => (clone $pendientes)->whereRaw("{$fecha} > ?", [$hoy->toDateString()]),
            'en_proceso' => Pedido::where('estado', 'En proceso'),
        ];
        $resumen = [];
        foreach ($consultas as $clave => $consulta) {
            $resumen[$clave] = (clone $consulta)->count();
        }

        $query = $periodo === 'todos'
            ? Pedido::whereIn('estado', ['Pendiente', 'En proceso'])
            : clone $consultas[$periodo];
        $pedidos = $query->with(['cliente', 'detalles.producto'])
            ->orderByRaw("{$fecha} ASC")->orderBy('fecha_entrega')->orderBy('id')
            ->paginate(12)->withQueryString();
        $grupos = $pedidos->getCollection()->groupBy(function ($pedido) use ($hoy) {
            if ($pedido->estado === 'En proceso') {
                return 'en_proceso';
            }
            $preparacion = $pedido->fecha_preparacion ?? $pedido->fecha_entrega;

            return $preparacion->lt($hoy) ? 'atrasados' : ($preparacion->isSameDay($hoy) ? 'hoy' : 'proximos');
        });
        $faltantesPorPedido = $pedidos->getCollection()->mapWithKeys(
            fn ($pedido) => [$pedido->id => $pedido->faltantesPreparacion()]
        );

        return view('pedidos.agenda', compact('periodo', 'hoy', 'resumen', 'pedidos', 'grupos', 'faltantesPorPedido'));
    }
}
