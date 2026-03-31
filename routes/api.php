<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/tasks/report', [TaskController::class, 'dailyReport']);

Route::get('/tasks',              [TaskController::class, 'index']);
Route::post('/tasks',             [TaskController::class, 'store']);
Route::patch('/tasks/{id}/status',[TaskController::class, 'updateStatus']);
Route::delete('/tasks/{id}',      [TaskController::class, 'destroy']);