<?php

use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/feedback', [FeedbackController::class, 'store'])->middleware('role:trainer');
    Route::get('/feedback', [FeedbackController::class, 'index'])->middleware('role:admin');
});
