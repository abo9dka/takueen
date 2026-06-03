<?php

use App\Http\Controllers\RoadmapController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/get-roadmaps', [RoadmapController::class, 'index']);
    Route::get('/display-roadmaps/{id}', [RoadmapController::class, 'show']);

    Route::middleware('role:admin,supervisor')->group(function () {
        Route::post('/create-roadmaps', [RoadmapController::class, 'store']);
        Route::put('/edit-roadmaps/{id}', [RoadmapController::class, 'update']);
        Route::patch('/update-roadmaps/{id}', [RoadmapController::class, 'update']);
        Route::delete('/delete-roadmaps/{id}', [RoadmapController::class, 'destroy']);
        Route::post('/roadmaps/ai-generate', [RoadmapController::class, 'aiGenerate']);
    });
});
