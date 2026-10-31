<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShipmentExceptionController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('public.track');
});

Route::get('/track', [PublicTrackingController::class, 'index'])->name('public.track');
Route::get('/track/{number}', [PublicTrackingController::class, 'show'])->name('public.track.show');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick-login');

// Global currency switcher (publicly accessible or authenticated)
Route::post('/currency/switch', [CurrencyController::class, 'switchCurrency'])->name('currency.switch');

/*
|--------------------------------------------------------------------------
| Authenticated Application Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Shipments & Operations
    Route::resource('shipments', ShipmentController::class);
    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.status');
    Route::post('/shipments/{shipment}/tracking', [TrackingController::class, 'store'])->name('shipments.tracking.store');
    Route::post('/shipments/{shipment}/exceptions', [ShipmentExceptionController::class, 'store'])->name('shipments.exceptions.store');
    Route::patch('/exceptions/{exception}/resolve', [ShipmentExceptionController::class, 'resolve'])->name('exceptions.resolve');

    // Carriers & Locations
    Route::get('/carriers', [CarrierController::class, 'index'])->name('carriers.index');
    Route::post('/carriers', [CarrierController::class, 'store'])->name('carriers.store');
    Route::put('/carriers/{carrier}', [CarrierController::class, 'update'])->name('carriers.update');

    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');

    // Currencies & Exchange Rates
    Route::get('/currencies', [CurrencyController::class, 'index'])->name('currencies.index');
    Route::patch('/currencies/{currency}', [CurrencyController::class, 'updateRate'])->name('currencies.update-rate');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Audit Logs (Admin)
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
