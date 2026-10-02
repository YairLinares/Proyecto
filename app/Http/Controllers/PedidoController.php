<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Insumo;
use App\Models\MovimientoInsumo;
use App\Models\Pedido;
use App\Models\Producto;
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

        $query = Pedido::with(['cliente', 'detalles.producto'])->withExists('movimientosInsumo');

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
        $validated['insumos_reservados_at'] = now();

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

                if (! $insumo || $insumo->stockLibre() < round($cantidadAUsar, 2)) {
                    $nombre = $insumo?->nombre ?? 'un insumo requerido';
                    $unidad = $insumo?->unidad ?? '';
                    $disponible = $insumo ? number_format(max(0, $insumo->stockLibre()), 2, ',', '.') : '0';
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
                $pedido->reservas()->create([
                    'insumo_id' => $insumoId,
                    'cantidad' => round($cantidadAUsar, 2),
                ]);
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
        $pedido->load('cliente', 'detalles.producto', 'pagos', 'usuario', 'reservas.insumo');
        $pedido->loadExists('movimientosInsumo');

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

        if ($request->has('productos')) {
            throw ValidationException::withMessages(['productos' => 'Los productos de un pedido con reserva o consumo no se pueden cambiar desde esta pantalla.']);
        }

        DB::transaction(function () use ($pedido, $validated) {
            $pedido = Pedido::whereKey($pedido->id)->lockForUpdate()->firstOrFail();
            $this->transicionar($pedido, $validated['estado']);
            $datos = $validated;
            unset($datos['estado']);
            $pedido->update($datos);
        });

        // Los productos solo se reemplazan cuando el formulario los envía.
        // La edición rápida no incluye productos y debe conservar los existentes.
        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido actualizado correctamente.');
    }

    /**
     * Eliminar pedido
     */
    public function destroy(Pedido $pedido)
    {
        if (! in_array($pedido->estado, ['Pendiente', 'Cancelado'], true) || $pedido->produccion_iniciada_at || MovimientoInsumo::where('pedido_id', $pedido->id)->exists()) {
            return back()->with('error', 'Solo puedes eliminar pedidos pendientes o cancelados sin consumo registrado.');
        }

        DB::transaction(function () use ($pedido) {
            $pedido = Pedido::whereKey($pedido->id)->lockForUpdate()->firstOrFail();
            if (! in_array($pedido->estado, ['Pendiente', 'Cancelado'], true) || $pedido->produccion_iniciada_at || MovimientoInsumo::where('pedido_id', $pedido->id)->exists()) {
                throw ValidationException::withMessages(['estado' => 'Este pedido tiene consumo registrado o no puede eliminarse en su estado actual.']);
            }
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

        DB::transaction(function () use ($pedido, $validated) {
            $pedido = Pedido::whereKey($pedido->id)->lockForUpdate()->firstOrFail();
            $this->transicionar($pedido, $validated['estado']);
        });

        return back()->with('success', 'Estado del pedido actualizado.');
    }

    private function transicionar(Pedido $pedido, string $nuevoEstado): void
    {
        if ($pedido->estado === $nuevoEstado) {
            return;
        }

        $permitidos = [
            'Pendiente' => ['En proceso', 'Cancelado'],
            'En proceso' => ['Completado', 'Cancelado'],
            'Completado' => ['Cancelado'],
            'Cancelado' => [],
        ];
        if (! in_array($nuevoEstado, $permitidos[$pedido->estado] ?? [], true)) {
            throw ValidationException::withMessages(['estado' => 'El cambio de estado solicitado no está permitido.']);
        }

        if ($pedido->insumos_reservados_at && $pedido->estado === 'Pendiente') {
            if ($nuevoEstado === 'En proceso') {
                $reservas = $pedido->reservas()->orderBy('insumo_id')->lockForUpdate()->get();
                foreach ($reservas as $reserva) {
                    $insumo = Insumo::whereKey($reserva->insumo_id)->lockForUpdate()->firstOrFail();
                    // La liberación y el consumo suceden juntos o se revierten juntos.
                    $reserva->delete();
                    if ($insumo->stockLibre() < (float) $reserva->cantidad) {
                        throw ValidationException::withMessages(['estado' => "No hay suficiente stock vigente de {$insumo->nombre} para iniciar la producción."]);
                    }
                    MovimientoInsumo::registrar(
                        $insumo,
                        'Salida',
                        (float) $reserva->cantidad,
                        "Producción del pedido {$pedido->numero_pedido}",
                        Auth::id(),
                        $pedido->id,
                    );
                }
                $pedido->produccion_iniciada_at = now();
            } elseif ($nuevoEstado === 'Cancelado') {
                $pedido->reservas()->delete();
            }
        }

        // Los pedidos anteriores ya tenían insumos descontados; no se consumen otra vez.
        $pedido->estado = $nuevoEstado;
        $pedido->save();
    }
}
