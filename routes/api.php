<?php

use App\Http\Controllers\api\DataCenter\SensorController;
use App\Http\Controllers\api\monitoreo\CameraController;
use App\Http\Controllers\api\monitoreo\DesperfectosController;
use App\Http\Controllers\api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// monitoreo
Route::prefix('monitoreo')->group(function () {
    Route::get('/camaras', [CameraController::class, 'index']);
    Route::post('/fallas/down', [DesperfectosController::class,'store']);
    Route::post('/fallas/up', [DesperfectosController::class,'update']);
});


Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('datacenter/temperatura', [SensorController::class, 'getTemperatura']);
});
