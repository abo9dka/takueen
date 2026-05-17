<?php

use App\Http\Controllers\CompetitionController;
use Illuminate\Support\Facades\Route;

//all Competition
Route::get('/get-competitions', [CompetitionController::class, 'index']);
//single Competition
Route::get('/display-competitions/{id}', [CompetitionController::class, 'show']);
//CREATE
Route::post('/create-competition', [CompetitionController::class, 'store']);
//UPDATE
Route::put('/put-competitions/{id}', [CompetitionController::class, 'update']);
//DELETE
Route::delete('/delete-competitions/{id}', [CompetitionController::class, 'destroy']);
