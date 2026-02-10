<?php

use App\Http\Controllers\api\monitoreo\CameraController;
use App\Http\Controllers\api\monitoreo\DesperfectosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// monitoreo
Route::prefix('monitoreo')->group(function () {
    Route::get('/camaras', [CameraController::class, 'index']);
    Route::post('/fallas/down', [DesperfectosController::class,'store']);
    Route::post('/fallas/up', [DesperfectosController::class,'update']);
});

