<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\FeriaController;
use App\Http\Controllers\FiscalizacionController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\IncidenciasFeriaController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/calendar-events', [CalendarController::class, 'index']);
Route::post('/calendar-events', [CalendarController::class, 'store']);

Route::get('/audits', [AuditoriaController::class, 'index'])->name('auditorias');

Route::get('usuarios/datatables', [UsuarioController::class, 'datatables'])->name('usuarios.datatables');

Route::get('/ferias', [FeriaController::class, 'index'])->name('ferias.index');
Route::get('/ferias/index-all', [FeriaController::class, 'indexAll'])->name('ferias.index-all');
Route::get('/ferias/datatables', [FeriaController::class, 'datatables'])->name('ferias.datatables');
Route::get('/ferias/datatablesAll', [FeriaController::class, 'datatablesAll'])->name('ferias.datatablesAll');
Route::post('/ferias', [FeriaController::class, 'store'])->name('ferias.store');
Route::get('/ferias/{id}/edit', [FeriaController::class, 'edit'])->name('ferias.edit');
Route::put('/ferias/{id}', [FeriaController::class, 'update'])->name('ferias.update');
Route::delete('/ferias/{id}', [FeriaController::class, 'destroy'])->name('ferias.destroy');
//Route::post('/ferias/massive', [FeriaController::class, 'storeMassive'])->name('ferias.storeMassive');
Route::post('/ferias/store-massive', [FeriaController::class, 'storeMassive'])->name('ferias.storeMassive');
Route::post('/ferias/check-duplicates', [FeriaController::class, 'checkDuplicates'])->name('ferias.checkDuplicates');


Route::prefix('incidencias_ferias')->group(function () {
    Route::get('/', [IncidenciasFeriaController::class, 'index'])->name('incidencias_ferias.index');
    Route::get('/datatables', [IncidenciasFeriaController::class, 'datatables'])->name('incidencias_ferias.datatables');
    Route::get('/create', [IncidenciasFeriaController::class, 'create'])->name('incidencias_ferias.create'); // Opcional, si usas vista
    Route::post('/store', [IncidenciasFeriaController::class, 'store'])->name('incidencias_ferias.store');
    Route::get('/show/{incidenciasFeria}', [IncidenciasFeriaController::class, 'show'])->name('incidencias_ferias.show');
    Route::get('/edit/{incidenciasFeria}', [IncidenciasFeriaController::class, 'edit'])->name('incidencias_ferias.edit'); // Opcional, si usas vista
    Route::put('/update/{incidenciasFeria}', [IncidenciasFeriaController::class, 'update'])->name('incidencias_ferias.update');
    Route::delete('/destroy/{incidenciasFeria}', [IncidenciasFeriaController::class, 'destroy'])->name('incidencias_ferias.destroy');
    Route::post('/get-feria', [IncidenciasFeriaController::class, 'getFeriaByCedula'])->name('incidencias_ferias.options');
    Route::get('/active-workers', [IncidenciasFeriaController::class, 'getActiveWorkers'])->name('incidencias_ferias.active_workers');
    //Route::post('/ferias/check-cedula', [IncidenciasFeriaController::class, 'checkCedula']);
});

// Shared route for massive import
// Routes for fiscalizacions-all (all records, #fiscalizacionesTableAll)
Route::group(['prefix' => 'fiscalizacions-all', 'as' => 'fiscalizacions-all.'], function () {
    Route::get('/', [FiscalizacionController::class, 'indexAll'])->name('index');
    Route::get('/datatable', [FiscalizacionController::class, 'datatablesAll'])->name('datatables');
    Route::get('/create', [FiscalizacionController::class, 'createAll'])->name('create');
    Route::post('/', [FiscalizacionController::class, 'storeAll'])->name('store');
    Route::get('/{id}/edit', [FiscalizacionController::class, 'editAll'])->name('edit');
    Route::put('/{id}', [FiscalizacionController::class, 'updateAll'])->name('update');
    Route::delete('/{id}', [FiscalizacionController::class, 'destroyAll'])->name('destroy');
    Route::post('/check-duplicates', [FiscalizacionController::class, 'checkDuplicates'])->name('checkDuplicates');
    Route::post('/store-massive', [FiscalizacionController::class, 'storeMassive'])->name('storeMassive');
});

// Routes for fiscalizacions (active records, #fiscalizacionesTable)
Route::group(['prefix' => 'fiscalizacions', 'as' => 'fiscalizacions.'], function () {
    Route::get('/', [FiscalizacionController::class, 'index'])->name('index');
    Route::get('/datatable', [FiscalizacionController::class, 'datatables'])->name('datatables');
    Route::get('/create', [FiscalizacionController::class, 'create'])->name('create');
    Route::post('/', [FiscalizacionController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [FiscalizacionController::class, 'edit'])->name('edit');
    Route::put('/{id}', [FiscalizacionController::class, 'update'])->name('update');
    Route::delete('/{id}', [FiscalizacionController::class, 'destroy'])->name('destroy');
    Route::post('/check-duplicates', [FiscalizacionController::class, 'checkDuplicates'])->name('checkDuplicates');
    Route::post('/store-massive', [FiscalizacionController::class, 'storeMassive'])->name('storeMassive');
});

use App\Http\Controllers\IncidenciasFiscalizacionController;

Route::prefix('incidencias_fiscalizaciones')->group(function () {
    Route::get('/', [IncidenciasFiscalizacionController::class, 'index'])->name('incidencias_fiscalizaciones.index');
    Route::get('/datatables', [IncidenciasFiscalizacionController::class, 'datatables'])->name('incidencias_fiscalizaciones.datatables');
    Route::get('/create', [IncidenciasFiscalizacionController::class, 'create'])->name('incidencias_fiscalizaciones.create'); // Opcional, si usas vista
    Route::post('/store', [IncidenciasFiscalizacionController::class, 'store'])->name('incidencias_fiscalizaciones.store');
    Route::get('/show/{incidenciasFiscalizacion}', [IncidenciasFiscalizacionController::class, 'show'])->name('incidencias_fiscalizaciones.show');
    Route::get('/edit/{incidenciasFiscalizacion}', [IncidenciasFiscalizacionController::class, 'edit'])->name('incidencias_fiscalizaciones.edit'); // Opcional, si usas vista
    Route::put('/update/{incidenciasFiscalizacion}', [IncidenciasFiscalizacionController::class, 'update'])->name('incidencias_fiscalizaciones.update');
    Route::delete('/destroy/{incidenciasFiscalizacion}', [IncidenciasFiscalizacionController::class, 'destroy'])->name('incidencias_fiscalizaciones.destroy');
    Route::post('/get-fiscalizacion', [IncidenciasFiscalizacionController::class, 'getFiscalizacionByCedula'])->name('incidencias_fiscalizaciones.options');
    Route::get('/active-workers', [IncidenciasFiscalizacionController::class, 'getActiveWorkers'])->name('incidencias_fiscalizaciones.active_workers');
    // Route::post('/fiscalizaciones/check-cedula', [IncidenciasFiscalizacionController::class, 'checkCedula']); // Opcional, si aplica
});

// Authenticated routes
Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', RolController::class);
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('ferias', FeriaController::class);

});
