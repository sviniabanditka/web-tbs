<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Common\Controllers\HealthController;

// Basic ping route
Route::get('ping', fn() => response()->json(['message' => 'pong']));

// Full health check route
Route::get('health', [HealthController::class, 'check']);
