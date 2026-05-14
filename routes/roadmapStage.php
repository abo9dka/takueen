<?php
use App\Http\Controllers\RoadmapStageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// single stage
Route::get('/display-stages/{id}', [RoadmapStageController::class, 'show']);
// CREATE
Route::post('/create-stages', [RoadmapStageController::class, 'store']);
// UPDATE
Route::put('/edit-stages/{id}', [RoadmapStageController::class, 'update']);
Route::patch('/update-stages/{id}', [RoadmapStageController::class, 'update']);
// DELETE
Route::delete('/delete-stages/{id}', [RoadmapStageController::class, 'destroy']);

