<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

Route::get('/', [SensorController::class, 'index'])->name('dashboard');
Route::get('/test-api', function () {
    return view('test-api');
})->name('test-api');
