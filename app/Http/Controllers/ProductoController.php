<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Insumo;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Mostrar lista de productos
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoria = $request->get('categoria');

        $query = Producto::with('categoria');

        if ($search) {
            $query->where('nombre', 'like', "%$search%");
        }

        if ($categoria) {
            $query->where('categoria_id', $categoria);
        }

        $productos = $query->paginate(10);
        $categorias = Categoria::all();

        return view('productos.index', compact('productos', 'categorias', 'search', 'categoria'));
    }

    /**
     * Mostrar formulario para crear producto
     */
    public function create()
    {
        $categorias = Categoria::all();
        $insumos = Insumo::all();
        return view('productos.create', compact('categorias', 'insumos'));
    }

    /**
     * Guardar nuevo producto
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|unique:productos|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'costo_produccion' => 'required|numeric|min:0',
            'stock_disponible' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'tiempo_preparacion_dias' => 'required|integer|min:1',
            'unidad_medida' => 'required|in:Unidad,Kg,Gramos,Litros,Mililitros',
        ]);

        $producto = Producto::create($validated);

        // Asociar insumos si se envían
        if ($request->has('insumos')) {
            foreach ($request->insumos as $insumoId => $cantidad) {
                if ($cantidad > 0) {
                    $producto->insumos()->attach($insumoId, ['cantidad_necesaria' => $cantidad]);
                }
            }
        }

        return redirect()->route('productos.show', $producto)->with('success', 'Producto creado correctamente.');
    }

    /**
     * Mostrar detalles de un producto
     */
    public function show(Producto $producto)
    {
        $producto->load('categoria', 'insumos');
        return view('productos.show', compact('producto'));
    }

    /**
     * Mostrar formulario para editar producto
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();
        $insumos = Insumo::all();
        return view('productos.edit', compact('producto', 'categorias', 'insumos'));
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|unique:productos,nombre,' . $producto->id . '|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'costo_produccion' => 'required|numeric|min:0',
            'stock_disponible' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'tiempo_preparacion_dias' => 'required|integer|min:1',
            'unidad_medida' => 'required|in:Unidad,Kg,Gramos,Litros,Mililitros',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $producto->update($validated);

        // Actualizar insumos
        $producto->insumos()->detach();
        if ($request->has('insumos')) {
            foreach ($request->insumos as $insumoId => $cantidad) {
                if ($cantidad > 0) {
                    $producto->insumos()->attach($insumoId, ['cantidad_necesaria' => $cantidad]);
                }
            }
        }

        return redirect()->route('productos.show', $producto)->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Eliminar producto
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}