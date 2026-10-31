<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarrierApiController;
use App\Http\Controllers\Api\CurrencyApiController;
use App\Http\Controllers\Api\ExceptionApiController;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\ShipmentApiController;
use App\Http\Controllers\Api\TrackingApiController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Public API Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Global Currency Rates & Conversion API
    Route::get('/currencies', [CurrencyApiController::class, 'index'])->name('currencies.index');
    Route::post('/currencies/convert', [CurrencyApiController::class, 'convert'])->name('currencies.convert');

    /*
    |--------------------------------------------------------------------------
    | Protected Sanctum API Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/user', [AuthController::class, 'user'])->name('user');

        // Shipments CRUD
        Route::apiResource('shipments', ShipmentApiController::class);

        // Tracking Events
        Route::get('/shipments/{shipment}/tracking', [TrackingApiController::class, 'index'])->name('shipments.tracking.index');
        Route::post('/shipments/{shipment}/tracking', [TrackingApiController::class, 'store'])->name('shipments.tracking.store');

        // Exceptions
        Route::get('/shipments/{shipment}/exceptions', [ExceptionApiController::class, 'index'])->name('shipments.exceptions.index');
        Route::post('/shipments/{shipment}/exceptions', [ExceptionApiController::class, 'store'])->name('shipments.exceptions.store');
        Route::patch('/exceptions/{exception}', [ExceptionApiController::class, 'update'])->name('exceptions.update');

        // Carriers & Locations
        Route::get('/carriers', [CarrierApiController::class, 'index'])->name('carriers.index');
        Route::post('/carriers', [CarrierApiController::class, 'store'])->name('carriers.store');

        Route::get('/locations', [LocationApiController::class, 'index'])->name('locations.index');
        Route::post('/locations', [LocationApiController::class, 'store'])->name('locations.store');
    });
});
