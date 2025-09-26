<?php

use Illuminate\Support\Facades\Route;
use App\Modules\MapGeneration\Controllers\MapGenerationController;

// Public map generation route
Route::get('/map', [MapGenerationController::class, 'generate']);
