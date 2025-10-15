<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('cities', CityController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::post('cities/{city}/favorite', [CityController::class, 'favorite']);
    Route::delete('cities/{city}/favorite', [CityController::class, 'unfavorite']);

    Route::get('weather/{city}', [WeatherController::class, 'current']);

    Route::get('alerts', [AlertController::class, 'index']);
    Route::post('alerts', [AlertController::class, 'store']);
    Route::delete('alerts/{alert}', [AlertController::class, 'destroy']);
});
