<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('role:trainer')->group(function () {
        Route::get('/placement-test/{fieldId}', [StudentController::class, 'placementTest']);
        Route::post('/finish-test/{fieldId}', [StudentController::class, 'finishTest']);
    });
});
