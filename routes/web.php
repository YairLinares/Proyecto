<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MovimientoInsumoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;

Route::get('/', function () {
    return redirect('/login');
});

Route::view('/compartir', 'compartir')->name('compartir');

// Rutas de Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth', \App\Http\Middleware\ExclusiveAdminSession::class])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/contrasena', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Clientes
    Route::resource('clientes', ClienteController::class);

    Route::middleware('admin')->group(function () {
        Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
        Route::get('/usuarios/nuevo', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/usuarios', [AuthController::class, 'register'])->name('usuarios.store');
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/ventas.csv', [ReporteController::class, 'exportarVentas'])->name('reportes.ventas.csv');
        // Categorías
        Route::resource('categorias', CategoriaController::class);

        // Productos
        Route::resource('productos', ProductoController::class);

        // Insumos
        Route::get('/insumos/{insumo}/movimientos/nuevo', [MovimientoInsumoController::class, 'create'])->name('insumos.movimientos.create');
        Route::post('/insumos/{insumo}/movimientos', [MovimientoInsumoController::class, 'store'])->name('insumos.movimientos.store');
        Route::resource('insumos', InsumoController::class);
        Route::patch('/insumos/{insumo}/lotes/{lote}', [InsumoController::class, 'actualizarLote'])->name('insumos.lotes.update');
    });

    // Pedidos
    Route::resource('pedidos', PedidoController::class);
    Route::patch('/pedidos/{pedido}/cambiar-estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiarEstado');

    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');

});
