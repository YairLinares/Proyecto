<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', '¡Bienvenido!');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Sesión cerrada.');
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Procesar registro
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'telefono' => 'nullable|string',
            'ciudad' => 'nullable|string',
            'cargo' => 'required|in:Administrador,Empleado',
            'codigo_administrador' => 'required_if:cargo,Administrador|nullable|string',
        ]);

        if ($validated['cargo'] === 'Administrador' && $validated['codigo_administrador'] !== env('ADMIN_REGISTRATION_CODE')) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation', 'codigo_administrador']))
                ->withErrors(['codigo_administrador' => 'El codigo de autorizacion no es correcto.']);
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['name'] = trim($validated['nombre'] . ' ' . $validated['apellido']);
        unset($validated['codigo_administrador']);

        User::create($validated);

        return redirect()->route('login')->with('success', 'Registro exitoso. Por favor, inicia sesión.');
    }
}
