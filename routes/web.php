<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ConversacionController;
use App\Http\Controllers\EmprendimientoController;
use App\Http\Controllers\EmprendimientoPublicoController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\MiTiendaMetricasController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PedidoEmprendedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ResenaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/
Route::get('/', [InicioController::class, 'index'])->name('inicio');
Route::get('/productos', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/productos/{producto}', [CatalogoController::class, 'show'])->name('catalogo.producto');
Route::get('/emprendimientos', [EmprendimientoPublicoController::class, 'index'])->name('emprendimientos.index');
Route::get('/emprendimientos/{emprendimiento}', [EmprendimientoPublicoController::class, 'show'])->name('emprendimientos.show');

/*
|--------------------------------------------------------------------------
| Panel (redirección según rol)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->emprendimientoGestionable()) {
        return redirect()->route('mi-tienda.index');
    }

    return redirect()->route('inicio');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rutas autenticadas (comprador, vendedor y admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Perfil (RF-06, RF-08)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Carrito (RF-35)
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar/{producto}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::patch('/carrito/{producto}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
    Route::delete('/carrito/{producto}', [CarritoController::class, 'quitar'])->name('carrito.quitar');
    Route::post('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');

    // Pedidos del comprador (RF-36 a RF-44)
    Route::middleware('verified')->group(function () {
        Route::post('/pedidos', [PedidoController::class, 'store'])
            ->middleware('throttle:pedidos')->name('pedidos.store');
    });
    Route::get('/mis-pedidos', [PedidoController::class, 'index'])->name('mis-pedidos.index');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::post('/pedidos/{pedido}/cancelar', [PedidoController::class, 'cancelar'])->name('pedidos.cancelar');

    // Reseñas (RF-45, RF-46)
    Route::post('/pedidos/{pedido}/resena', [ResenaController::class, 'store'])
        ->middleware('throttle:mensajes')->name('resenas.store');

    // Reportes (RF-50, RF-51)
    Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');

    // Chat (RF-29 a RF-34)
    Route::get('/conversaciones', [ConversacionController::class, 'index'])->name('conversaciones.index');
    Route::get('/conversaciones/no-leidos', [ConversacionController::class, 'noLeidos'])->name('conversaciones.no-leidos');
    Route::post('/conversaciones/iniciar/{emprendimiento}', [ConversacionController::class, 'iniciar'])->name('conversaciones.iniciar');
    Route::get('/conversaciones/{conversacion}', [ConversacionController::class, 'show'])->name('conversaciones.show');
    Route::post('/conversaciones/{conversacion}/mensajes', [ConversacionController::class, 'store'])
        ->middleware('throttle:mensajes')->name('conversaciones.mensajes');
    Route::get('/conversaciones/{conversacion}/poll', [ConversacionController::class, 'poll'])->name('conversaciones.poll');
});

/*
|--------------------------------------------------------------------------
| Activación del emprendimiento (RF-09 a RF-12)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/vender', [EmprendimientoController::class, 'crearForm'])->name('emprendimientos.crear-form');
    Route::post('/vender', [EmprendimientoController::class, 'store'])->name('emprendimientos.store');
    Route::get('/mi-emprendimiento', [EmprendimientoController::class, 'editar'])->name('emprendimientos.editar');
    Route::put('/mi-emprendimiento', [EmprendimientoController::class, 'update'])->name('emprendimientos.update');
    Route::post('/mi-emprendimiento/pausar', [EmprendimientoController::class, 'pausar'])->name('emprendimientos.pausar');
    Route::post('/mi-emprendimiento/reactivar', [EmprendimientoController::class, 'reactivar'])->name('emprendimientos.reactivar');
});

/*
|--------------------------------------------------------------------------
| Mi tienda (vendedor, RF-15 a RF-22, RF-41 a RF-44, RF-47 a RF-49)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'emprendedor'])->prefix('mi-tienda')->group(function () {
    Route::get('/', [ProductoController::class, 'index'])->name('mi-tienda.index');
    Route::get('/productos/crear', [ProductoController::class, 'create'])->name('mi-tienda.productos.crear');
    Route::post('/productos', [ProductoController::class, 'store'])->name('mi-tienda.productos.store');
    Route::get('/productos/{producto}/editar', [ProductoController::class, 'edit'])->name('mi-tienda.productos.editar');
    Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('mi-tienda.productos.update');
    Route::patch('/productos/{producto}/estado', [ProductoController::class, 'cambiarEstado'])->name('mi-tienda.productos.estado');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('mi-tienda.productos.destroy');

    Route::get('/metricas', [MiTiendaMetricasController::class, 'index'])->name('mi-tienda.metricas');

    Route::get('/pedidos', [PedidoEmprendedorController::class, 'index'])->name('mi-tienda.pedidos.index');
    Route::get('/pedidos/{pedido}', [PedidoEmprendedorController::class, 'show'])->name('mi-tienda.pedidos.show');
    Route::post('/pedidos/{pedido}/confirmar', [PedidoEmprendedorController::class, 'confirmar'])->name('mi-tienda.pedidos.confirmar');
    Route::post('/pedidos/{pedido}/rechazar', [PedidoEmprendedorController::class, 'rechazar'])->name('mi-tienda.pedidos.rechazar');
    Route::post('/pedidos/{pedido}/entregar', [PedidoEmprendedorController::class, 'entregar'])->name('mi-tienda.pedidos.entregar');
    Route::post('/pedidos/{pedido}/cancelar', [PedidoEmprendedorController::class, 'cancelar'])->name('mi-tienda.pedidos.cancelar');
});

/*
|--------------------------------------------------------------------------
| Administración (RF-50 a RF-57)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/aprobacion', [Admin\AprobacionController::class, 'index'])->name('aprobacion');
    Route::post('/aprobacion/{emprendimiento}/aprobar', [Admin\AprobacionController::class, 'aprobar'])->name('aprobacion.aprobar');
    Route::post('/aprobacion/{emprendimiento}/rechazar', [Admin\AprobacionController::class, 'rechazar'])->name('aprobacion.rechazar');

    Route::get('/emprendimientos', [Admin\EmprendimientoController::class, 'index'])->name('emprendimientos.index');
    Route::post('/emprendimientos/{emprendimiento}/destacar', [Admin\EmprendimientoController::class, 'destacar'])->name('emprendimientos.destacar');
    Route::post('/emprendimientos/{emprendimiento}/suspender', [Admin\EmprendimientoController::class, 'suspender'])->name('emprendimientos.suspender');
    Route::post('/emprendimientos/{emprendimiento}/reactivar', [Admin\EmprendimientoController::class, 'reactivar'])->name('emprendimientos.reactivar');

    Route::get('/categorias', [Admin\CategoriaController::class, 'index'])->name('categorias.index');
    Route::post('/categorias', [Admin\CategoriaController::class, 'store'])->name('categorias.store');
    Route::put('/categorias/{categoria}', [Admin\CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{categoria}', [Admin\CategoriaController::class, 'destroy'])->name('categorias.destroy');

    Route::get('/reportes', [Admin\ReporteController::class, 'index'])->name('reportes.index');
    Route::post('/reportes/{reporte}/resolver', [Admin\ReporteController::class, 'resolver'])->name('reportes.resolver');

    Route::get('/usuarios', [Admin\UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios/{usuario}/toggle', [Admin\UsuarioController::class, 'toggleActivo'])->name('usuarios.toggle');
    Route::delete('/usuarios/{usuario}', [Admin\UsuarioController::class, 'destroy'])->name('usuarios.destroy');
});

require __DIR__.'/auth.php';
