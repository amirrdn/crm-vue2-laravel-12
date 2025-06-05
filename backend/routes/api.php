<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CallLogController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::get('/users', [UserController::class, 'index']);

    Route::get('/contacts/positions', [ContactController::class, 'getPositions']);
    Route::apiResource('contacts', ContactController::class);
    Route::post('/contacts/{id}/toggle-favorite', [ContactController::class, 'toggleFavorite']);

    Route::apiResource('call-logs', CallLogController::class);
    Route::get('/call-logs/stream', [CallLogController::class, 'stream']);
}); 