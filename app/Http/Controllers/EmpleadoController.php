<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $datos = $request->validate(['buscar' => 'nullable|string|max:100']);
        $buscar = trim($datos['buscar'] ?? '');

        $empleados = User::query()
            ->where('cargo', 'Empleado')
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($coincidencias) use ($buscar) {
                    $coincidencias->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('apellido', 'like', "%{$buscar}%")
                        ->orWhere('name', 'like', "%{$buscar}%")
                        ->orWhere('username', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $empleadosTotal = User::where('cargo', 'Empleado')->count();

        return view('empleados.index', compact('empleados', 'empleadosTotal', 'buscar'));
    }
}
