<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\EventoControlador;
use App\Http\Controllers\FotografiaController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\PuntosController;
use App\Http\Controllers\TestEstudianteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Pagina Principal de laravel
Route::get('/', function () {
    return view('welcome');
});


// Sistema de rutas para LARAVEL
// End point login
Route::post('/api/login', [UserController::class, 'login']);

// Rutas portegidas
Route::group(['middleware' => ['api.auth']], function () {

    // /*************RUTAS PROTEGIDAS PARA USUARIOS********/ 
    Route::resource('/api/user', UserController::class);
    Route::post('/api/user/buscarusuarios', [UserController::class, 'buscarUsuarios']);
    Route::post('/api/user/changespassword', [UserController::class, 'changesPassword']);
    Route::post('/api/user/changespassword', [UserController::class, 'changesPassword']);

    // /*************RUTAS PROTEGIDAS PARA EVENTOS********/ 
    Route::resource('/api/eventos', EventoControlador::class);
    Route::post('/api/eventos/cambiarestado', [EventoControlador::class, 'cambiarEstadoConcluido']);

    // /*************RUTAS PROTEGIDAS PARA PUNTOS********/ 
    Route::resource('/api/puntos', PuntosController::class);
    Route::get('/api/puntos/indexpuntoseventos/{id}', [PuntosController::class, 'indexPuntosEventos']);

    // /*************RUTAS PROTEGIDAS PARA AGENDAS********/ 
    Route::resource('/api/agendas', AgendaController::class);
    Route::post('/api/agendas/destroyagenda', [AgendaController::class, 'destroyAgenda']);
    Route::post('/api/agendas/destroyallpoinst', [AgendaController::class, 'destroyAllPoinst']);

    // /*************RUTAS PROTEGIDAS PARA USUARIOS********/ 
    // Route::resource('/api/prueba', PruebaController::class);
    Route::post('/api/prueba/buscarpruebas', [PruebaController::class, 'buscarPruebas']);

    // /*************RUTAS PROTEGIDAS PARA TEST-ESTUDIANTES********/ 
    Route::post('/api/test/indextestprueba', [TestEstudianteController::class, 'indexTestPrueba']);
    Route::post('/api/test/buscartest', [TestEstudianteController::class, 'buscarTest']);

    // /*************RUTAS PROTEGIDAS PARA VISITANTES********/ 
    Route::resource('/api/visitas', IngresoController::class);
    Route::post('/api/visitas/buscarvisitas', [IngresoController::class, 'buscarVisitas']);
    Route::put('/api/visitas/fechasalida/{id}', [IngresoController::class, 'fechaSalida']);
    Route::get('/api/visitas/historial/{id}', [IngresoController::class, 'historial']);
    Route::get('/api/visitas/historialreal/{carnet}', [IngresoController::class, 'historialReal']);

    // /*************RUTAS PROTEGIDAS PARA FOTOGRAFIAS********/ 
    Route::resource('/api/fotografia', FotografiaController::class);
});
Route::resource('/api/test', TestEstudianteController::class);
Route::resource('/api/prueba', PruebaController::class);
