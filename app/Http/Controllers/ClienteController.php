<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Mostrar lista de clientes
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'filter' => 'nullable|in:todos,activo,inactivo',
            'page' => 'nullable|integer|min:1',
        ]);
        $search = $request->get('search');
        $filter = $request->get('filter') ?? 'todos';

        $query = Cliente::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%$search%")
                  ->orWhere('telefono_principal', 'like', "%$search%");
            });
        }

        if ($filter != 'todos') {
            $query->where('estado', $filter);
        }

        $clientes = $query->paginate(10);
        $totalClientes = Cliente::count();
        $clientesVIP = Cliente::where('tipo_cliente', 'Corporativo')->count();
        $totalCompras = Cliente::sum('total_compras');

        return view('clientes.index', compact('clientes', 'totalClientes', 'clientesVIP', 'totalCompras', 'search', 'filter'));
    }

    /**
     * Mostrar formulario para crear cliente
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Guardar nuevo cliente
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'telefono_principal' => ['required', 'string', 'size:8', 'regex:/^[0-9]{8}$/'],
            'tipo_cliente' => 'nullable|in:Regular,Corporativo',
            'direccion' => 'nullable|string|max:16000',
        ]);

        $validated['tipo_cliente'] = $validated['tipo_cliente'] ?? 'Regular';
        $validated['direccion'] = $validated['direccion'] ?? 'No especificada';

        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }

    /**
     * Mostrar detalles de un cliente
     */
    public function show(Cliente $cliente)
    {
        $cliente->loadSum(['pedidos as total_compras' => fn ($query) => $query->where('estado', 'Completado')], 'total');
        $cliente->total_compras = (float) ($cliente->total_compras ?? 0);
        $pedidos = $cliente->pedidos()->latest()->paginate(10);
        return view('clientes.show', compact('cliente', 'pedidos'));
    }

    /**
     * Mostrar formulario para editar cliente
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'telefono_principal' => ['required', 'string', 'size:8', 'regex:/^[0-9]{8}$/'],
            'tipo_cliente' => 'nullable|in:Regular,Corporativo',
            'direccion' => 'nullable|string|max:16000',
            'estado' => 'nullable|in:activo,inactivo',
        ]);

        $validated['tipo_cliente'] = $validated['tipo_cliente'] ?? 'Regular';
        $validated['direccion'] = $validated['direccion'] ?? 'No especificada';

        $validated['estado'] = $validated['estado'] ?? $cliente->estado;
        $cliente->update($validated);

        return redirect()->route('clientes.show', $cliente)->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Eliminar cliente
     */
    public function destroy(Cliente $cliente)
    {
        if ($cliente->pedidos()->exists()) {
            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar este cliente porque tiene pedidos registrados.');
        }

        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }
}
