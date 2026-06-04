<?php

use App\Http\Controllers\CompetitionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/competitions', [CompetitionController::class, 'index']);
    Route::get('/competitions/{id}', [CompetitionController::class, 'show']);
    Route::middleware('role:trainer')->group(function () {
        Route::post('/competitions/{id}/join', [CompetitionController::class, 'join']);
        Route::delete('/competitions/{id}/leave', [CompetitionController::class, 'leave']);
         });

    Route::middleware('role:admin')->group(function () {      
        Route::post('/competitions', [CompetitionController::class, 'store']);
        Route::put('/competitions/{id}', [CompetitionController::class, 'update']);
        Route::delete('/competitions/{id}', [CompetitionController::class, 'destroy']);
        Route::patch('/competitions/{id}/assign-supervisor', [CompetitionController::class, 'assignSupervisor']);
         });
});   