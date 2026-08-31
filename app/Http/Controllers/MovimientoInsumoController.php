<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\MovimientoInsumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovimientoInsumoController extends Controller
{
    public function create(Insumo $insumo)
    {
        return view('insumos.movimientos.create', compact('insumo'));
    }

    public function store(Request $request, Insumo $insumo)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:Entrada,Salida,Ajuste',
            'cantidad' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($insumo, $validated) {
                $insumo = Insumo::whereKey($insumo->id)->lockForUpdate()->firstOrFail();

                MovimientoInsumo::registrar(
                    $insumo,
                    $validated['tipo'],
                    (float) $validated['cantidad'],
                    $validated['motivo'],
                    Auth::id(),
                    null,
                    null,
                    $validated['tipo'] === 'Ajuste' ? (float) $validated['cantidad'] : null,
                );
            });
        } catch (\InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['cantidad' => $exception->getMessage()]);
        }

        return redirect()->route('insumos.show', $insumo)->with('success', 'Movimiento de inventario registrado correctamente.');
    }
}
