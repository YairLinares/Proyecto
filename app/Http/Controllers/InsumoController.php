<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use Illuminate\Http\Request;

class InsumoController extends Controller
{
    /**
     * Mostrar lista de insumos
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filter = $request->get('filter', 'todos');

        $query = Insumo::query();

        if ($search) {
            $query->where('nombre', 'like', "%$search%")
                  ->orWhere('proveedor', 'like', "%$search%");
        }

        if ($filter === 'critico') {
            $query->whereColumn('stock_actual', '<=', 'stock_minimo');
        } elseif ($filter != 'todos') {
            $query->where('estado', $filter);
        }

        $insumos = $query->paginate(10);
        $totalInsumos = Insumo::count();
        $stockBajo = Insumo::where('estado', 'Stock bajo')->count();
        $agotados = Insumo::where('estado', 'Agotado')->count();

        return view('insumos.index', compact('insumos', 'totalInsumos', 'stockBajo', 'agotados', 'search', 'filter'));
    }

    /**
     * Mostrar formulario para crear insumo
     */
    public function create()
    {
        return view('insumos.create');
    }

    /**
     * Guardar nuevo insumo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:insumos|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad' => 'required|in:Kg,Gramos,Litros,Mililitros,Unidad',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'proveedor' => 'required|string|max:255',
        ]);

        $insumo = Insumo::create($validated);
        $insumo->actualizarEstado();

        return redirect()->route('insumos.show', $insumo)->with('success', 'Insumo registrado correctamente.');
    }

    /**
     * Mostrar detalles de un insumo
     */
    public function show(Insumo $insumo)
    {
        $productos = $insumo->productos()->paginate(10);
        return view('insumos.show', compact('insumo', 'productos'));
    }

    /**
     * Mostrar formulario para editar insumo
     */
    public function edit(Insumo $insumo)
    {
        return view('insumos.edit', compact('insumo'));
    }

    /**
     * Actualizar insumo
     */
    public function update(Request $request, Insumo $insumo)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:insumos,nombre,' . $insumo->id . '|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad' => 'required|in:Kg,Gramos,Litros,Mililitros,Unidad',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'precio_unitario' => 'required|numeric|min:0',
            'proveedor' => 'required|string|max:255',
        ]);

        $insumo->update($validated);
        $insumo->actualizarEstado();

        return redirect()->route('insumos.show', $insumo)->with('success', 'Insumo actualizado correctamente.');
    }

    /**
     * Eliminar insumo
     */
    public function destroy(Insumo $insumo)
    {
        if ($insumo->productos()->count() > 0) {
            return back()->with('error', 'No puedes eliminar un insumo que está siendo usado en productos.');
        }

        $insumo->delete();
        return redirect()->route('insumos.index')->with('success', 'Insumo eliminado correctamente.');
    }
}