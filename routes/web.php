<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CityController;


Route::get('/', function () {
    return redirect()->route('cities.index');
});

// Automatikus Auth routeok (login, register, stb.)
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// --- Városok (Kliens) Route-ok ---

// 1. Publikus 
Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
Route::get('/cities/export/pdf', [CityController::class, 'exportPdf'])->name('cities.export.pdf');
Route::get('/cities/export/csv', [CityController::class, 'exportCsv'])->name('cities.export.csv');

// 2. Védett Route-ok (Csak bejelentkezve: Létrehozás, Módosítás, Törlés)
Route::middleware(['auth'])->group(function () {

    Route::resource('cities', CityController::class)->except(['index', 'show']);







});
