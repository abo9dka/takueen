<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
require __DIR__ . '/auth.php';

Route::get('/get-field', [FieldController::class, 'index']);
//Route::get('/diplay-field/{id}', [FieldController::class, 'show']);
Route::post('/create-field', [FieldController::class, 'store']);
Route::put('/edite-field/{id}', [FieldController::class, 'update']);
Route::delete('/delete-field/{id}', [FieldController::class, 'destroy']);
Route::get('/fields/{id}/questions', [FieldController::class, 'getFieldQuestions']);



Route::get('/get-question', [QuestionController::class, 'index']);
Route::get('/diplay-question/{id}', [QuestionController::class, 'show']);
Route::post('/create-question', [QuestionController::class, 'store']);
Route::put('/edite-question/{id}', [QuestionController::class, 'update']);
Route::delete('/delete-question/{id}', [QuestionController::class, 'destroy']);
