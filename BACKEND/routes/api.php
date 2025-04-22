<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ChechAuthMiddleware;

Route::post('v1/auth/signup', [AuthController::class, 'signup']);
Route::post('v1/auth/signin', [AuthController::class, 'signin']);

Route::middleware([ChechAuthMiddleware::class])->prefix('v1')->group(function () {
    Route::post('auth/signout', [AuthController::class, 'signout']);


    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::get('users/{username}', [GameController::class, 'shows']);

    Route::post('games', [GameController::class, 'store']);
    Route::put('games/{slug}', [GameController::class, 'update']);
    Route::delete('games/{slug}', [GameController::class, 'destroy']);
    Route::post('games/{slug}/scores', [GameController::class, 'addScore']);
    Route::post('games/{slug}/upload', [GameController::class, 'upload']);



    Route::get('admins', [AdminController::class, 'index']);
    Route::post('admins', [AdminController::class, 'store']);
    Route::get('admins/{id}', [AdminController::class, 'show']);
    Route::put('admins/{id}', [AdminController::class, 'update']);
    Route::delete('admins/{id}', [AdminController::class, 'destroy']);
});

Route::get('v1/games', [GameController::class, 'index']);
Route::get('v1/games/{slug}', [GameController::class, 'show']);
Route::get('v1/games/{slug}/scores', [GameController::class, 'scores']);

Route::fallback(function () {
    return response()->json([
        'status' => 'not found',
        'message' => 'not found',
    ], 404);
});
