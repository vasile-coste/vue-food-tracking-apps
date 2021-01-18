<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);

// Route::post('auth/register', 'UserController@register');
// Route::get('auth/register', 'UserController@index');

// // update user data
// Route::post('auth/{id}', 'UserController@update');

// // get user data
// Route::get('auth/{id}', 'UserController@getUser');

// //login
// Route::post('auth/login', 'UserController@login');