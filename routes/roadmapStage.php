<?php

use App\Http\Controllers\RoadmapStageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/stages', [RoadmapStageController::class, 'index']);
    Route::get('/display-stages/{id}', [RoadmapStageController::class, 'show']);
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::post('/create-stages', [RoadmapStageController::class, 'store']);
        Route::put('/edit-stages/{id}', [RoadmapStageController::class, 'update']);
        Route::delete('/delete-stages/{id}', [RoadmapStageController::class, 'destroy']);
    });
});
