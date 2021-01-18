<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SeedsController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/** User */
Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);

/** Farming Seeds */
Route::get('/farming/seeds/{user_id}', [SeedsController::class, 'seeds']);
Route::post('/farming/seeds/add', [SeedsController::class, 'newSeed']);
Route::post('/farming/seeds/update', [SeedsController::class, 'updateSeed']);
Route::post('/farming/seeds/delete', [SeedsController::class, 'deleteSeed']);

