<?php


use App\Http\Controllers\RoadmapController;
use Illuminate\Support\Facades\Route;

//  all roadmaps
Route::get('/get-roadmaps', [RoadmapController::class, 'index']);

// single roadmap
Route::get('/display-roadmaps/{id}', [RoadmapController::class, 'show']);

// CREATE 
Route::post('/create-roadmaps', [RoadmapController::class, 'store']);
// UPDATE
Route::put('/edit-roadmaps/{id}', [RoadmapController::class, 'update']);
Route::patch('/update-roadmaps/{id}', [RoadmapController::class, 'update']);
// DELETE 
Route::delete('/delete-roadmaps/{id}', [RoadmapController::class, 'destroy']);