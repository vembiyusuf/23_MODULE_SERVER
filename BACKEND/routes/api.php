<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('v1/auth/signup', [AuthController::class, 'signup']);
Route::post('v1/auth/signin', [AuthController::class, 'signin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('v1/auth/signout', [AuthController::class, 'signout']);

    Route::get('v1/users', [UserController::class, 'index']);
    Route::get('v1/users/{id}', [UserController::class, 'show']);
    Route::post('v1/users', [UserController::class, 'store']);
    Route::put('v1/users/{id}', [UserController::class, 'update']);
    Route::delete('v1/users/{id}', [UserController::class, 'destroy']);
});

Route::prefix('v1')->group(function () {
    Route::get('admins', [AdminController::class, 'index']);
    Route::post('admins', [AdminController::class, 'store']);
    Route::get('admins/{id}', [AdminController::class, 'show']);
    Route::put('admins/{id}', [AdminController::class, 'update']);
    Route::delete('admins/{id}', [AdminController::class, 'destroy']);
});


Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/games', [GameController::class, 'index']);
        Route::post('/games', [GameController::class, 'store']);
        Route::get('/games/{slug}', [GameController::class, 'show']);
        Route::put('/games/{slug}', [GameController::class, 'update']);
        Route::delete('/games/{slug}', [GameController::class, 'destroy']);
        Route::get('/games/{slug}/scores', [GameController::class, 'scores']);
        Route::post('/games/{slug}/scores', [GameController::class, 'addScore']);
        Route::post('/games/{slug}/upload', [GameController::class, 'upload']);
    });
});
