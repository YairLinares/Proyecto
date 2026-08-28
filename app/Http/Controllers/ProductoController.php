<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller
{
    /**
     * Mostrar lista de productos
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoria = $request->get('categoria');

        $query = Producto::with('categoria')->withCount('insumos');

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
            'tiempo_preparacion_dias' => 'required|integer|min:1',
            'insumos' => 'nullable|array',
            'insumos.*' => 'nullable|numeric|min:0',
        ]);

        [, $costoProduccion] = $this->prepararReceta($validated['insumos'] ?? []);
        unset($validated['insumos']);
        $validated['costo_produccion'] = $costoProduccion;
        $validated['stock_disponible'] = 0;
        $validated['stock_minimo'] = 0;
        $validated['unidad_medida'] = 'Unidad';

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
        $producto->load('insumos');
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
            'tiempo_preparacion_dias' => 'required|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
            'insumos' => 'nullable|array',
            'insumos.*' => 'nullable|numeric|min:0',
        ]);

        [, $costoProduccion] = $this->prepararReceta($validated['insumos'] ?? []);
        unset($validated['insumos']);
        $validated['costo_produccion'] = $costoProduccion;
        $validated['unidad_medida'] = 'Unidad';

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

    private function prepararReceta(array $cantidades): array
    {
        $cantidades = collect($cantidades)->filter(fn ($cantidad) => (float) $cantidad > 0);

        if ($cantidades->isEmpty()) {
            throw ValidationException::withMessages([
                'insumos' => 'Agrega al menos un insumo y su cantidad para la receta.',
            ]);
        }

        $insumos = Insumo::whereIn('id', $cantidades->keys())->get()->keyBy('id');

        if ($insumos->count() !== $cantidades->count()) {
            throw ValidationException::withMessages([
                'insumos' => 'Uno de los insumos seleccionados ya no existe.',
            ]);
        }

        $receta = [];
        $costoProduccion = 0;

        foreach ($cantidades as $insumoId => $cantidad) {
            $insumo = $insumos->get((int) $insumoId);
            $cantidad = (float) $cantidad;
            $receta[$insumo->id] = ['cantidad_necesaria' => $cantidad];
            $costoProduccion += (float) $insumo->precio_unitario * $cantidad;
        }

        return [$receta, round($costoProduccion, 2)];
    }
}
