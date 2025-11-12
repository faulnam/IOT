<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route untuk menerima data dari Wokwi (POST)
Route::post('/sensor-data', [SensorController::class, 'store']);

// Route untuk mendapatkan semua data sensor (GET)
Route::get('/sensor-data', [SensorController::class, 'getData']);

// Route untuk mendapatkan data sensor terbaru (GET)
Route::get('/sensor-data/latest', [SensorController::class, 'getLatest']);

// Route untuk mendapatkan statistik data sensor (GET)
Route::get('/sensor-data/stats', [SensorController::class, 'getStats']);
