<?php

use App\Http\Controllers\StatsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/stats', [StatsController::class, 'index']);
    Route::get('/leaderboard', [UserController::class, 'leaderboard']);
    Route::get('/trainees', [UserController::class, 'trainees']);
    Route::get('/supervisors', [UserController::class, 'supervisors']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::get('/supervisorsById/{id}', [UserController::class, 'supervisorsById']);
    Route::get('/supervisors/{id}/trainees', [UserController::class, 'supervisorTrainees']);

    Route::middleware('role:trainer')->group(function () {
        Route::get('/student/dashboard', [StudentController::class, 'studentDashboard']);
        Route::get('/student/fields', [StudentController::class, 'myFields']);
        Route::post('/student/fields/join', [StudentController::class, 'joinField']);
    });
});
