<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Routing\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgot']);
Route::post('/reset-password', [AuthController::class, 'reset']);
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::post('/create-supervisor', [AuthController::class, 'createSupervisor']);
    });

    Route::get('/leaderboard', [UserController::class, 'leaderboard']);
    Route::get('/trainees', [UserController::class, 'trainees']);
    Route::get('/supervisors', [UserController::class, 'supervisors']);
});
