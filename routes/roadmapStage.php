<?php

use App\Http\Controllers\RoadmapStageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // single stage
    Route::get('/display-stages/{id}', [RoadmapStageController::class, 'show']);
    Route::middleware('role:admin,supervisor')->group(function () {
        // CREATE
        Route::post('/create-stages', [RoadmapStageController::class, 'store']);
        // UPDATE
        Route::put('/edit-stages/{id}', [RoadmapStageController::class, 'update']);
        // DELETE
        Route::delete('/delete-stages/{id}', [RoadmapStageController::class, 'destroy']);
    });
});
