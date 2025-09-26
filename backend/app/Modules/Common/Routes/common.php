<?php

use Illuminate\Support\Facades\Route;

// Basic ping route
Route::get('ping', fn() => response()->json(['message' => 'pong']));
