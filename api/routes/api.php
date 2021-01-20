<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SeedingController;

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

/** Farming Seeding */
Route::get('/farming/seeding/seed/{user_id}', [SeedingController::class, 'seeds']);
Route::post('/farming/seeding/seed/add', [SeedingController::class, 'newSeed']);
Route::post('/farming/seeding/seed/update', [SeedingController::class, 'updateSeed']);
Route::get('/farming/seeding/companies/{user_id}', [SeedingController::class, 'seedCompanies']);
Route::get('/farming/seeding/companies/{user_id}/{seed_id}', [SeedingController::class, 'seedCompaniesBySeed']);
Route::post('/farming/seeding/companies/add', [SeedingController::class, 'newSeedCompanies']);
Route::post('/farming/seeding/companies/update', [SeedingController::class, 'updateSeedCompanies']);

