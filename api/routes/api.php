<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SeedingController;
use App\Http\Controllers\FertilizingController;
use App\Http\Controllers\HarvestingController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\MapSettingsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PackController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\CustomerController;

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
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);
    Route::post('update-password', [UserController::class, 'updatePassword']);
});

// Route::get('/farming/seeding/seed/{user_id}', [SeedingController::class, 'seeds']);
/** Farming */
Route::group(['prefix' => 'farming'], function () {
    Route::group(['prefix' => 'map'], function () {
        Route::get('{user_id}', [MapSettingsController::class, 'map']);
        Route::post('update', [MapSettingsController::class, 'update']);
    });

    /** Seeding */
    Route::group(['prefix' => 'seeding'], function () {
        Route::group(['prefix' => 'seed'], function () {
            Route::get('{user_id}', [SeedingController::class, 'seeds']);
            Route::post('add', [SeedingController::class, 'newSeed']);
            Route::post('update', [SeedingController::class, 'updateSeed']);
        });

        Route::group(['prefix' => 'companies'], function () {
            Route::get('{user_id}', [SeedingController::class, 'companies']);
            Route::get('{user_id}/{seed_id}', [SeedingController::class, 'companiesBySeed']);
            Route::post('add', [SeedingController::class, 'newCompany']);
            Route::post('update', [SeedingController::class, 'updateCompany']);
            Route::post('delete', [SeedingController::class, 'deleteCompany']);
        });
    });

    /** Fertilizing */
    Route::group(['prefix' => 'fertilizing'], function () {
        Route::group(['prefix' => 'fertilizer'], function () {
            Route::get('{user_id}', [FertilizingController::class, 'fertilizers']);
            Route::post('add', [FertilizingController::class, 'newFertilizer']);
            Route::post('update', [FertilizingController::class, 'updateFertilizer']);
        });

        Route::group(['prefix' => 'companies'], function () {
            Route::get('{user_id}', [FertilizingController::class, 'companies']);
            Route::get('{user_id}/{fertilizer_id}', [FertilizingController::class, 'companiesByFertilizer']);
            Route::post('add', [FertilizingController::class, 'newCompany']);
            Route::post('update', [FertilizingController::class, 'updateCompany']);
            Route::post('delete', [FertilizingController::class, 'deleteCompany']);
        });
    });

    /** Harvesting companies */
    Route::group(['prefix' => 'harvesting'], function () {
        Route::group(['prefix' => 'companies'], function () {
            Route::get('{user_id}', [HarvestingController::class, 'companies']);
            Route::post('add', [HarvestingController::class, 'newCompany']);
            Route::post('update', [HarvestingController::class, 'updateCompany']);
            Route::post('delete', [HarvestingController::class, 'deleteCompany']);
        });
    });

    /** Batch - aka field name / task */
    Route::group(['prefix' => 'field'], function () {
        Route::post('/all', [FieldController::class, 'allFields']);
        Route::post('/add', [FieldController::class, 'addField']);
        Route::post('/update', [FieldController::class, 'updateField']);
        /** Map progress */
        Route::group(['prefix' => 'location'], function () {
            Route::get('{field_id}', [FieldController::class, 'locations']);
            Route::post('add', [FieldController::class, 'newLocation']);
        });
    });
});

/** Packaging and transport */
Route::group(['prefix' => 'packaging'], function () {
    /** Products */
    Route::group(['prefix' => 'product'], function () {
        Route::post('/add', [ProductController::class, 'addProduct']);
        Route::post('/update', [ProductController::class, 'updateProduct']);
        Route::post('/delete', [ProductController::class, 'deleteProduct']);
        Route::post('/all', [ProductController::class, 'getProducts']);
        Route::get('/fields', [ProductController::class, 'getFields']);
    });
    /** Packs */
    Route::group(['prefix' => 'packs'], function () {
        Route::post('/all', [PackController::class, 'getAllPackages']);
        Route::post('/add-pack-and-prods', [PackController::class, 'addPackage']); 
        Route::post('/update', [PackController::class, 'updatePackage']); 
        Route::post('/delete', [PackController::class, 'deletePackage']);
        Route::post('/add-products', [PackController::class, 'addProducts']); 
        Route::post('/remove-product', [PackController::class, 'removeProduct']); 
    });
    /** Packs */
    Route::group(['prefix' => 'ship'], function () {
        Route::post('/all', [TransportController::class, 'getAllTransports']);
        Route::post('/add-transport-and-packs', [TransportController::class, 'addTransport']); 
        Route::post('/update', [TransportController::class, 'updateTransport']); 
        Route::post('/update-status', [TransportController::class, 'updateTransportStatus']); 
        Route::post('/delete', [TransportController::class, 'deleteTransport']);
    });
});

/** Packaging and transport */
Route::group(['prefix' => 'customer'], function () {
    Route::get('/{qr}', [CustomerController::class, 'getData']);
});
