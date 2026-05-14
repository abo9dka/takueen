<?php

use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::get('/questions/{id}', [QuestionController::class, 'show']);

    Route::middleware('role:admin,supervisor')->group(function () {
        Route::post('/create-questions', [QuestionController::class, 'store']);
        Route::put('/update-questions/{id}', [QuestionController::class, 'update']);
        Route::delete('/delete-questions/{id}', [QuestionController::class, 'destroy']);
        Route::post('/questions/ai-generate', [QuestionController::class, 'aiGenerate']);
    });
});
