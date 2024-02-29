<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

    Route::controller(AuthController::class)
    ->prefix('auth')
    ->as('auth.')
    ->group(function (){
        Route::post('register','register');
        Route::post('login','login');
        Route::get('logout','logout');
    });

    Route::middleware('auth:sanctum')->group(function (){
        Route::apiResource('chat',ChatController::class)->only(['index','store','show']);
        Route::apiResource('chat_message',ChatMessageController::class)->only(['index','store','show']);
        Route::apiResource('user',UserController::class)->only(['index']);

    });
