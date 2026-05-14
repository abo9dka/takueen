<?php

use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('role:trainer')->group(function () {
        Route::post('/submissions', [SubmissionController::class, 'store']);
        Route::put('/submissions/{id}', [SubmissionController::class, 'update']);

        Route::post('/submissions/{id}/evaluate', [SubmissionController::class, 'evaluate'])
            ->middleware('throttle:5,1');
    });
});
