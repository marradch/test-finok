<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\PostController;
use App\Http\Middleware\IsAdmin;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [LoginController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['is-admin'])->get('posts/import', [PostController::class, 'import']);
    Route::get('weather', [PostController::class, 'weather']);
    Route::apiResource('posts', PostController::class);
});
