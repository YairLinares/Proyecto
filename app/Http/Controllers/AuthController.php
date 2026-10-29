<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminSessionLock;
use App\Services\EmployeeSessionLock;
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
            'username' => 'required|string',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $lock = app($request->user()->esAdministrador() ? AdminSessionLock::class : EmployeeSessionLock::class);
            if (! $lock->acquire($request)) {
                Auth::guard()->logoutCurrentDevice();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')
                    ->withErrors(['username' => $lock::MESSAGE]);
            }
            return redirect()->route('dashboard')->with('success', '¡Bienvenido!');
        }

        return back()->withErrors([
            'username' => 'El usuario o la contraseña no son correctos.',
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        app(AdminSessionLock::class)->release($request);
        app(EmployeeSessionLock::class)->release($request);
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
        return redirect()->route('empleados.index', ['nuevo' => 1]);
    }

    /**
     * Procesar registro
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|min:3|max:30|alpha_dash|unique:users',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'telefono' => 'required|digits:8',
            'ciudad' => 'required|string|max:100',
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max' => 'El campo :attribute no debe superar los :max caracteres.',
            'username.alpha_dash' => 'El usuario solo puede contener letras, números, guiones y guiones bajos.',
            'username.unique' => 'Ese nombre de usuario ya está registrado. Elige otro.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Ese correo electrónico ya está registrado. Usa otro.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'telefono.required' => 'Ingresa el celular del empleado.',
            'telefono.digits' => 'El celular del empleado debe tener 8 dígitos.',
            'ciudad.required' => 'Ingresa la ciudad del empleado.',
        ], [
            'username' => 'usuario',
            'nombre' => 'nombre',
            'apellido' => 'apellido',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'telefono' => 'celular del empleado',
            'ciudad' => 'ciudad',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['name'] = trim($validated['nombre'] . ' ' . $validated['apellido']);
        $validated['cargo'] = 'Empleado';

        User::create($validated);

        return redirect()->route('empleados.index')->with('success', 'Empleado registrado correctamente.');
    }
}
