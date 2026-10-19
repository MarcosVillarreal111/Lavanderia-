<?php

use App\Http\Controllers\AdminAuditoriaController;
use App\Http\Controllers\AdminFacturaController;
use App\Http\Controllers\AdminNotificacionController;
use App\Http\Controllers\AdminPedidoController;
use App\Http\Controllers\AdminRegistroController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ReciboController;
use App\Livewire\CrearTarjeta;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.pedidos.index')
            : view('dashboard');
    })->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/pedidos', [AdminPedidoController::class, 'index'])->name('pedidos.index');
        Route::post('/pedidos/{pedido}/avanzar', [AdminPedidoController::class, 'avanzar'])->name('pedidos.avanzar');
        Route::post('/pedidos/{pedido}/retroceder', [AdminPedidoController::class, 'retroceder'])->name('pedidos.retroceder');
        Route::post('/pedidos/{pedido}/archivar', [AdminPedidoController::class, 'archivar'])->name('pedidos.archivar');

        Route::get('/registros', [AdminRegistroController::class, 'index'])->name('registros.index');

        Route::get('/auditoria', [AdminAuditoriaController::class, 'index'])->name('auditoria.index');

        Route::get('/facturas/{pedido}', [AdminFacturaController::class, 'show'])->name('facturas.show');

        Route::get('/notificaciones', [AdminNotificacionController::class, 'index'])->name('notificaciones.index');
        Route::post('/notificaciones/leer', [AdminNotificacionController::class, 'marcarLeidas'])->name('notificaciones.leer');
    });

    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::post('/pedidos/repetir/{pedido}', [PedidoController::class, 'repetir'])->name('pedidos.repetir');

    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/pagos/tarjetas/crear', CrearTarjeta::class)->name('pagos.tarjetas.create');

    Route::get('/direcciones', [DireccionController::class, 'index'])->name('direcciones.index');
    Route::get('/direcciones/crear', [DireccionController::class, 'create'])->name('direcciones.create');
    Route::post('/direcciones', [DireccionController::class, 'store'])->name('direcciones.store');
    Route::get('/direcciones/{direccion}/editar', [DireccionController::class, 'edit'])->name('direcciones.edit');
    Route::put('/direcciones/{direccion}', [DireccionController::class, 'update'])->name('direcciones.update');
    Route::delete('/direcciones/{direccion}', [DireccionController::class, 'destroy'])->name('direcciones.destroy');

    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/notificaciones', [PerfilController::class, 'actualizarNotificaciones'])->name('perfil.notificaciones');

    Route::get('/recibos/{pago}/descargar', [ReciboController::class, 'descargar'])->name('recibos.descargar');
});

Route::get('/terminos', function () {
    return view('terms');
})->name('terms.show');
