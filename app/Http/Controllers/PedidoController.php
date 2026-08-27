<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetallePedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'fecha_entrega' => 'nullable|date',
            'descripcion_especificaciones' => 'nullable|string',
            'direccion_entrega' => 'nullable|string',
            'telefono_contacto' => 'nullable|string',
            'metodo_pago' => 'required|in:Efectivo,Tarjeta,Transferencia',
            'anticipo_recibido' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'costo_envio' => 'nullable|numeric|min:0',
            'productos' => 'required|array|min:1',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $cliente = Cliente::findOrFail($validated['cliente_id']);
        $productos = Producto::whereIn('id', array_keys($validated['productos']))
            ->where('estado', 'activo')
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

        $pedido->subtotal = $subtotal;
        $pedido->calcularTotal();
        $pedido->save();

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
            'metodo_pago' => 'required|in:Efectivo,Tarjeta,Transferencia',
            'anticipo_recibido' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'costo_envio' => 'nullable|numeric|min:0',
            'estado' => 'required|in:Pendiente,En proceso,Completado,Cancelado',
        ]);

        $pedido->update($validated);

        // Actualizar detalles
        $pedido->detalles()->delete();
        $subtotal = 0;
        if ($request->has('productos')) {
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
        }
        $pedido->subtotal = $subtotal;
        $pedido->calcularTotal();
        $pedido->save();

        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido actualizado correctamente.');
    }

    /**
     * Eliminar pedido
     */
    public function destroy(Pedido $pedido)
    {
        if ($pedido->estado != 'Pendiente') {
            return back()->with('error', 'Solo puedes eliminar pedidos en estado Pendiente.');
        }

        $pedido->delete();
        return redirect()->route('pedidos.index')->with('success', 'Pedido eliminado correctamente.');
    }

    /**
     * Cambiar estado de pedido
     */
    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $pedido->update(['estado' => $request->estado]);
        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
