<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SquadController;

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
Route::post('/login', [LoginController::class, 'login']);
Route::get('/login/verify', [LoginController::class, 'verify']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::apiResource('roles', RoleController::class)->except(['create', 'edit', 'show', 'update', 'destroy', 'store']);
    Route::apiResource('users', UserController::class)->except(['create', 'edit']);
    Route::apiResource('squads', SquadController::class)->except(['create', 'edit']);
});
