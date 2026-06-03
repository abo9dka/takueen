<?php

use App\Http\Controllers\FieldController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/details/{id}', [FieldController::class, 'detailsField']);
    Route::get('/get-fields', [FieldController::class, 'index']);
    Route::get('/fields/{id}/questions', [FieldController::class, 'getFieldQuestions']);
    Route::get('/supervisorsByField/{fieldId}', [UserController::class, 'supervisorsByField']);

    Route::middleware('role:admin,supervisor')->group(function () {
        Route::post('/create-field', [FieldController::class, 'store']);
        Route::put('/update-fields/{id}', [FieldController::class, 'update']);
        Route::delete('/delete-fields/{id}', [FieldController::class, 'destroy']);
    });
    Route::post('/supervisors/{id}/fields', [FieldController::class, 'assignFields'])->middleware('role:admin');
});
