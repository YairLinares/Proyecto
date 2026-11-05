<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    /**
     * Mostrar lista de categorías
     */
    public function index()
    {
        $categorias = Categoria::withCount('productos')->paginate(10);
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Mostrar formulario para crear categoría
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Guardar nueva categoría
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:categorias|string|max:255',
            'descripcion' => 'nullable|string|max:16000',
        ]);

        $validated['slug'] = $this->slugDisponible($validated['nombre']);

        Categoria::create($validated);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Mostrar detalles de una categoría
     */
    public function show(Categoria $categoria)
    {
        $productos = $categoria->productos()->paginate(10);
        return view('categorias.show', compact('categoria', 'productos'));
    }

    /**
     * Mostrar formulario para editar categoría
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Actualizar categoría
     */
    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre' => 'required|unique:categorias,nombre,' . $categoria->id . '|string|max:255',
            'descripcion' => 'nullable|string|max:16000',
        ]);

        $validated['slug'] = $this->slugDisponible($validated['nombre'], $categoria->id);

        $categoria->update($validated);

        return redirect()->route('categorias.show', $categoria)->with('success', 'Categoría actualizada correctamente.');
    }

    private function slugDisponible(string $nombre, ?int $ignorarId = null): string
    {
        $base = Str::limit(Str::slug($nombre) ?: 'sabor', 240, '');
        $slug = $base;
        $sufijo = 2;
        while (Categoria::where('slug', $slug)
            ->when($ignorarId, fn ($query) => $query->where('id', '!=', $ignorarId))
            ->exists()) {
            $slug = $base.'-'.$sufijo++;
        }

        return $slug;
    }

    /**
     * Eliminar categoría
     */
    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->count() > 0) {
            return back()->with('error', 'No puedes eliminar una categoría con productos.');
        }

        $categoria->delete();
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
