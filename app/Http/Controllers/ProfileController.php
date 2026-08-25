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
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telefono' => 'nullable|string|max:20',
            'ciudad' => 'nullable|string|max:255',
            'cargo' => 'nullable|string|max:255',
            'biografia' => 'nullable|string',
        ]);

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
            'contrasena_actual' => 'required',
            'nueva_contrasena' => 'required|min:8|confirmed',
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