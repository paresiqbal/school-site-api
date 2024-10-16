<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('news', NewsController::class)->except(['update']);
Route::post('/news/{news}', [NewsController::class, 'update'])->middleware('auth:sanctum');

Route::apiResource('announcement', AnnouncementController::class)->except(['update']);
Route::post('/announcement/{announcement}', [AnnouncementController::class, 'update'])->middleware('auth:sanctum');

Route::apiResource('agenda', AgendaController::class);
