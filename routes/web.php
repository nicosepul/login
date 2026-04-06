<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Register
Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

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
});

