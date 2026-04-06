<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\VeterinariaController;
use App\Http\Controllers\RegisterController;

Route::get('/razas', [VeterinariaController::class, 'obtenerRazas']);
Route::get('/mascotas', [VeterinariaController::class, 'obtenerMascotas']);
Route::post('/registro-mascota-dueno', [VeterinariaController::class, 'registroMascotaDueno']);
Route::get('/mascotas-por-rut', [VeterinariaController::class, 'obtenerMascotasPorRut']);
Route::post('/registrar-ingreso', [VeterinariaController::class, 'registrarIngreso']);
Route::get('/ingresos', [VeterinariaController::class, 'obtenerIngresos']);
Route::put('/mascota/{id}', [VeterinariaController::class, 'actualizarMascota']);
Route::delete('/mascota/{id}', [VeterinariaController::class, 'eliminarMascota']);
Route::get('/countries', [RegisterController::class, 'countries']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


