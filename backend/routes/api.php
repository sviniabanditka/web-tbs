<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameActionController;
use App\Http\Controllers\MapGenerationController;

Route::get('ping', fn() => response()->json(['message' => 'pong']));

Route::post('send-login-code', [AuthController::class, 'sendLoginCode']);
Route::post('login-with-code', [AuthController::class, 'loginWithCode']);

Route::get('/map', [MapGenerationController::class, 'generate']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);

    // Game Management
    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);
    Route::get('/games/{game}', [GameController::class, 'show']);
    Route::post('/games/{game}/join', [GameController::class, 'join']);
    Route::post('/games/{game}/leave', [GameController::class, 'leave']);
    Route::post('/games/{game}/start', [GameController::class, 'start']);

    // Map and Hex Data
    Route::get('/games/{game}/map', [GameController::class, 'getMap']);
    Route::get('/games/{game}/hex/{q}/{r}', [GameController::class, 'getHex']);

    // Game Actions
    Route::post('/games/{game}/actions', [GameActionController::class, 'execute'])->name('game.action.execute');
});
