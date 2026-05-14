<?php

use App\Http\Controllers\FieldController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/get-fields', [FieldController::class, 'index']);
    Route::get('/fields/{id}/questions', [FieldController::class, 'getFieldQuestions']);

    Route::middleware('role:admin,supervisor')->group(function () {
        Route::post('/create-field', [FieldController::class, 'store']);
        Route::put('/update-fields/{id}', [FieldController::class, 'update']);
        Route::delete('/delete-fields/{id}', [FieldController::class, 'destroy']);
    });
});
