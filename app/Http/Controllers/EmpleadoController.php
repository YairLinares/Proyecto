<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function destroy(User $empleado)
    {
        if ($empleado->cargo !== 'Empleado') {
            abort(404);
        }

        DB::transaction(function () use ($empleado) {
            // Cerrar cualquier sesión activa antes de retirar la cuenta.
            DB::table('sessions')->where('user_id', $empleado->id)->delete();
            DB::table('admin_session_lock')
                ->where('id', 2)
                ->where('user_id', $empleado->id)
                ->update(['user_id' => null, 'token' => null, 'last_activity' => 0, 'session_id' => null]);
            DB::table('password_reset_tokens')->where('email', $empleado->email)->delete();
            $empleado->delete();
        });

        return redirect()->route('empleados.index')->with('success', 'Cuenta del empleado eliminada correctamente.');
    }
}
