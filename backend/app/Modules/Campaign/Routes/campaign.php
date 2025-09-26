<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Campaign\Controllers\CampaignController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Campaign Management
    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::post('/campaigns', [CampaignController::class, 'store']);
    Route::get('/campaigns/{campaign}', [CampaignController::class, 'show']);
    Route::post('/campaigns/{campaign}/join', [CampaignController::class, 'join']);
    Route::post('/campaigns/{campaign}/leave', [CampaignController::class, 'leave']);
    Route::post('/campaigns/{campaign}/start', [CampaignController::class, 'start']);
});
