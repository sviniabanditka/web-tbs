<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;

// Public auth routes
Route::post('send-login-code', [AuthController::class, 'sendLoginCode']);
Route::post('login-with-code', [AuthController::class, 'loginWithCode']);

// Protected auth routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user', [AuthController::class, 'getUser']);
});
