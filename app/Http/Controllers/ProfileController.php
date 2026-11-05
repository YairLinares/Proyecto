<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Mostrar la vista de perfil
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Actualizar información personal
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'ciudad' => 'nullable|string|max:255',
        ]);

        $validated['name'] = trim($validated['nombre'].' '.($validated['apellido'] ?? ''));
        if (mb_strlen($validated['name']) > 255) {
            throw \Illuminate\Validation\ValidationException::withMessages(['nombre' => 'El nombre y apellido juntos no deben superar los 255 caracteres.']);
        }

        $user->update($validated);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Cambiar contraseña
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'contrasena_actual' => 'required|string',
            'nueva_contrasena' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['contrasena_actual'], $user->password)) {
            return back()->withErrors(['contrasena_actual' => 'La contraseña actual no es correcta.']);
        }

        $user->update([
            'password' => Hash::make($validated['nueva_contrasena']),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
