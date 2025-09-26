<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Campaign\Controllers\CampaignController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Campaign Management
    Route::get('/', [CampaignController::class, 'index']);
    Route::post('/', [CampaignController::class, 'store']);
    Route::get('{campaign}', [CampaignController::class, 'show']);
    Route::post('{campaign}/join', [CampaignController::class, 'join']);
    Route::post('{campaign}/leave', [CampaignController::class, 'leave']);
    Route::post('{campaign}/start', [CampaignController::class, 'start']);
});
