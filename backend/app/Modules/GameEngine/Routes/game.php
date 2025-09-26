<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // Game Engine routes will be added here
    Route::get('/games', function () {
        return response()->json(['message' => 'Game Engine routes loaded successfully!']);
    });
});
