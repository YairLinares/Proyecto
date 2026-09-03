<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetallePedido;
use App\Models\Insumo;
use App\Models\MovimientoInsumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PedidoController extends Controller
{
    /**
     * Mostrar lista de pedidos
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $estado = $request->get('estado', 'todos');
        $fecha = $request->get('fecha');
        $entrega = $request->get('entrega');

        $query = Pedido::with(['cliente', 'detalles.producto']);

        if ($search) {
            $query->where('numero_pedido', 'like', "%$search%")
                  ->orWhereHas('cliente', function ($q) use ($search) {
                      $q->where('nombre_completo', 'like', "%$search%");
                  });
        }

        if ($estado != 'todos') {
            $query->where('estado', $estado);
        }

        if ($fecha === 'hoy') {
            $query->whereDate('created_at', today());
        } elseif ($fecha === 'semana') {
            $query->whereBetween('fecha_pedido', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        if ($entrega === 'hoy') {
            $query->whereDate('fecha_entrega', today());
        }

        $pedidos = $query->latest()->paginate(10);
        $totalPedidos = Pedido::count();
        $pedidosActivos = Pedido::whereIn('estado', ['Pendiente', 'En proceso'])->count();
        $clientesNuevos = Cliente::whereMonth('created_at', now()->month)->count();

        return view('pedidos.index', compact('pedidos', 'totalPedidos', 'pedidosActivos', 'clientesNuevos', 'search', 'estado'));
    }

    /**
     * Mostrar formulario para crear pedido
     */
    public function create()
    {
        $clientes = Cliente::all();
        $productos = Producto::where('estado', 'activo')->get();
        return view('pedidos.create', compact('clientes', 'productos'));
    }

    /**
     * Guardar nuevo pedido
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_pedido' => 'required|in:Personalizado,Predefinido',
            'prioridad' => 'required|in:Bajo,Normal,Alto',
            'fecha_entrega' => 'required|date|after_or_equal:today',
            'descripcion_especificaciones' => 'nullable|string',
            'direccion_entrega' => 'nullable|string',
            'telefono_contacto' => 'nullable|string',
            'metodo_pago' => 'required|in:Efectivo',
            'anticipo_recibido' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'costo_envio' => 'nullable|numeric|min:0',
            'productos' => 'required|array|min:1',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $cliente = Cliente::findOrFail($validated['cliente_id']);
        $productos = Producto::whereIn('id', array_keys($validated['productos']))
            ->where('estado', 'activo')
            ->with('insumos')
            ->get()
            ->keyBy('id');

        if ($productos->count() !== count($validated['productos'])) {
            return back()->withInput()->withErrors([
                'productos' => 'Uno de los productos seleccionados ya no está disponible.',
            ]);
        }

        $validated['numero_pedido'] = Pedido::generarNumeroPedido();
        $validated['fecha_pedido'] = now()->toDateString();
        $validated['fecha_entrega'] = $validated['fecha_entrega'] ?? now()->toDateString();
        $validated['direccion_entrega'] = ($validated['direccion_entrega'] ?? null) ?: ($cliente->direccion ?: 'Por coordinar');
        $validated['telefono_contacto'] = ($validated['telefono_contacto'] ?? null) ?: ($cliente->telefono_principal ?: 'Por coordinar');
        $validated['anticipo_recibido'] = $validated['anticipo_recibido'] ?? 0;
        $validated['descuento'] = $validated['descuento'] ?? 0;
        $validated['costo_envio'] = $validated['costo_envio'] ?? 0;
        $validated['usuario_id'] = Auth::id();
        $validated['estado'] = 'Pendiente';

        $consumoPorInsumo = [];
        foreach ($validated['productos'] as $productoId => $datos) {
            $producto = $productos->get((int) $productoId);

            foreach ($producto->insumos as $insumo) {
                $cantidadNecesaria = (float) $insumo->pivot->cantidad_necesaria;

                if ($cantidadNecesaria <= 0) {
                    continue;
                }

                $consumoPorInsumo[$insumo->id] = ($consumoPorInsumo[$insumo->id] ?? 0)
                    + ($cantidadNecesaria * (int) $datos['cantidad']);
            }
        }

        $pedido = DB::transaction(function () use ($validated, $productos, $consumoPorInsumo) {
            $insumos = Insumo::whereIn('id', array_keys($consumoPorInsumo))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($consumoPorInsumo as $insumoId => $cantidadAUsar) {
                $insumo = $insumos->get($insumoId);

                if (! $insumo || (float) $insumo->stock_actual < $cantidadAUsar) {
                    $nombre = $insumo?->nombre ?? 'un insumo requerido';
                    $unidad = $insumo?->unidad ?? '';
                    $disponible = $insumo ? number_format((float) $insumo->stock_actual, 2, ',', '.') : '0';
                    $requerido = number_format($cantidadAUsar, 2, ',', '.');

                    throw ValidationException::withMessages([
                        'productos' => "No hay suficiente stock de {$nombre}. Se requieren {$requerido} {$unidad} y solo hay {$disponible}.",
                    ]);
                }
            }

            $pedido = Pedido::create($validated);
            $subtotal = 0;

            foreach ($validated['productos'] as $productoId => $datos) {
                $producto = $productos->get((int) $productoId);
                $detalle = new DetallePedido([
                    'producto_id' => $producto->id,
                    'cantidad' => $datos['cantidad'],
                    'precio_unitario' => $producto->precio_venta,
                ]);
                $detalle->calcularSubtotal();
                $pedido->detalles()->save($detalle);
                $subtotal += $detalle->subtotal;
            }

            foreach ($consumoPorInsumo as $insumoId => $cantidadAUsar) {
                $insumo = $insumos->get($insumoId);
                MovimientoInsumo::registrar(
                    $insumo,
                    'Salida',
                    $cantidadAUsar,
                    "Consumo automatico para {$pedido->numero_pedido}",
                    Auth::id(),
                    $pedido->id,
                );
            }

            $pedido->subtotal = $subtotal;
            $pedido->calcularTotal();
            $pedido->save();

            return $pedido;
        });

        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido creado correctamente.');
    }

    /**
     * Mostrar detalles de un pedido
     */
    public function show(Pedido $pedido)
    {
        $pedido->load('cliente', 'detalles.producto', 'pagos', 'usuario');
        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Mostrar formulario para editar pedido
     */
    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::all();
        $productos = Producto::where('estado', 'activo')->get();
        return view('pedidos.edit', compact('pedido', 'clientes', 'productos'));
    }

    /**
     * Actualizar pedido
     */
    public function update(Request $request, Pedido $pedido)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_pedido' => 'required|in:Personalizado,Predefinido',
            'prioridad' => 'required|in:Bajo,Normal,Alto',
            'fecha_entrega' => 'required|date',
            'descripcion_especificaciones' => 'nullable|string',
            'direccion_entrega' => 'required|string',
            'telefono_contacto' => 'required|string',
            'metodo_pago' => 'required|in:Efectivo',
            'anticipo_recibido' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'costo_envio' => 'nullable|numeric|min:0',
            'estado' => 'required|in:Pendiente,En proceso,Completado,Cancelado',
        ]);

        if ($pedido->estado === 'Cancelado' && $validated['estado'] !== 'Cancelado') {
            return back()->withInput()->with('error', 'Un pedido cancelado no puede reactivarse porque su stock ya fue devuelto.');
        }

        if ($pedido->estado !== 'Cancelado' && $validated['estado'] === 'Cancelado') {
            DB::transaction(function () use ($pedido) {
                $this->restaurarConsumosPedido($pedido, 'Devolucion por cancelacion del pedido');
            });
        }

        $pedido->update($validated);

        // Los productos solo se reemplazan cuando el formulario los envía.
        // La edición rápida no incluye productos y debe conservar los existentes.
        if ($request->has('productos')) {
            $pedido->detalles()->delete();
            $subtotal = 0;
            foreach ($request->productos as $productoId => $datos) {
                if ($datos['cantidad'] > 0) {
                    $producto = Producto::find($productoId);
                    $detalle = new DetallePedido([
                        'producto_id' => $productoId,
                        'cantidad' => $datos['cantidad'],
                        'precio_unitario' => $producto->precio_venta,
                    ]);
                    $detalle->calcularSubtotal();
                    $pedido->detalles()->save($detalle);
                    $subtotal += $detalle->subtotal;
                }
            }
            $pedido->subtotal = $subtotal;
            $pedido->calcularTotal();
            $pedido->save();
        }

        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido actualizado correctamente.');
    }

    /**
     * Eliminar pedido
     */
    public function destroy(Pedido $pedido)
    {
        if (! in_array($pedido->estado, ['Pendiente', 'Cancelado'], true)) {
            return back()->with('error', 'Solo puedes eliminar pedidos en estado Pendiente o Cancelado.');
        }

        DB::transaction(function () use ($pedido) {
            $this->restaurarConsumosPedido($pedido, 'Devolucion por eliminacion del pedido');
            $pedido->delete();
        });
        return redirect()->route('pedidos.index')->with('success', 'Pedido eliminado correctamente.');
    }

    /**
     * Cambiar estado de pedido
     */
    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $validated = $request->validate([
            'estado' => 'required|in:Pendiente,En proceso,Completado,Cancelado',
        ]);

        if ($pedido->estado === 'Cancelado' && $validated['estado'] !== 'Cancelado') {
            return back()->with('error', 'Un pedido cancelado no puede reactivarse porque su stock ya fue devuelto.');
        }

        DB::transaction(function () use ($pedido, $validated) {
            if ($pedido->estado !== 'Cancelado' && $validated['estado'] === 'Cancelado') {
                $this->restaurarConsumosPedido($pedido, 'Devolucion por cancelacion del pedido');
            }

            $pedido->update(['estado' => $validated['estado']]);
        });

        return back()->with('success', 'Estado del pedido actualizado.');
    }

    private function restaurarConsumosPedido(Pedido $pedido, string $motivo): void
    {
        $salidas = MovimientoInsumo::where('pedido_id', $pedido->id)
            ->where('tipo', 'Salida')
            ->whereNull('revertido_at')
            ->lockForUpdate()
            ->get();

        foreach ($salidas as $salida) {
            $insumo = Insumo::whereKey($salida->insumo_id)->lockForUpdate()->firstOrFail();

            MovimientoInsumo::registrar(
                $insumo,
                'Entrada',
                (float) $salida->cantidad,
                "{$motivo}: {$pedido->numero_pedido}",
                Auth::id(),
                $pedido->id,
                $salida->id,
            );

            $salida->update(['revertido_at' => now()]);
        }

        // Los pedidos creados antes de este historial ya descontaron stock,
        // pero no tienen movimientos guardados. Se devuelve su receta actual.
        if ($salidas->isEmpty()) {
            $pedido->loadMissing('detalles.producto.insumos');
            $cantidades = [];

            foreach ($pedido->detalles as $detalle) {
                foreach ($detalle->producto->insumos as $insumo) {
                    $cantidades[$insumo->id] = ($cantidades[$insumo->id] ?? 0)
                        + ((float) $insumo->pivot->cantidad_necesaria * (int) $detalle->cantidad);
                }
            }

            foreach ($cantidades as $insumoId => $cantidad) {
                $insumo = Insumo::whereKey($insumoId)->lockForUpdate()->firstOrFail();

                MovimientoInsumo::registrar(
                    $insumo,
                    'Entrada',
                    $cantidad,
                    "{$motivo}: {$pedido->numero_pedido} (pedido anterior)",
                    Auth::id(),
                    $pedido->id,
                );
            }
        }
    }
}
