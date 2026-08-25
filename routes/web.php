<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas de Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/contrasena', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Clientes
    Route::resource('clientes', ClienteController::class);

    // Categorías
    Route::resource('categorias', CategoriaController::class);

    // Productos
    Route::resource('productos', ProductoController::class);

    // Insumos
    Route::resource('insumos', InsumoController::class);

    // Pedidos
    Route::resource('pedidos', PedidoController::class);
    Route::patch('/pedidos/{pedido}/cambiar-estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiarEstado');

});