<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistroController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Register
Route::get('/register', [RegistroController::class, 'index'])->name('register');
Route::get('/registro-usuario', [RegistroController::class, 'index'])->name('registro-usuario');
Route::post('/register', [RegistroController::class, 'registrar'])->name('register.store');

Route::middleware('authUser')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
    Route::get('/test', function () {
        return view('test');
    })->name('test');
    Route::get('/registro', function () {
        return view('registro');
    })->name('registro');
    Route::get('/buscador', function () {
        return view('buscador');
    })->name('buscador');
    Route::get('/registro-ingreso', function () {
        return view('registro_ingreso');
    })->name('registro_ingreso');
    Route::get('/ingresos', function () {
        return view('ingresos');
    })->name('ingresos');
    Route::get('/usuarios', function () {
        return view('usuarios');
    })->name('usuarios');
    Route::get('/usuarios/listado', [RegistroController::class, 'listarUsuarios'])->name('usuarios.listado');
    Route::put('/usuarios/{usuario}', [RegistroController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
    Route::delete('/usuarios/{usuario}', [RegistroController::class, 'eliminarUsuario'])->name('usuarios.eliminar');
});

