<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShipmentExceptionController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Tracking Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('public.track');
})->name('home');

// Public tracker renders the dashboard with live tracker (accessible without login)
Route::get('/track', [DashboardController::class, 'index'])->name('public.track');
Route::get('/track/{number}', function (string $number) {
    return redirect()->route('public.track', ['track' => $number])->withFragment('live-tracker');
})->name('public.track.show');

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

    // Shipments & Operations (Operator & Admin manage; Customer views own)
    Route::resource('shipments', ShipmentController::class);
    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.status');
    Route::post('/shipments/{shipment}/tracking', [TrackingController::class, 'store'])->name('shipments.tracking.store');
    Route::post('/shipments/{shipment}/exceptions', [ShipmentExceptionController::class, 'store'])->name('shipments.exceptions.store');
    Route::patch('/exceptions/{exception}/resolve', [ShipmentExceptionController::class, 'resolve'])->name('exceptions.resolve');

    // Master Data: Users, Carriers & Locations (Administrator only)
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    Route::get('/carriers', [CarrierController::class, 'index'])->name('carriers.index');
    Route::post('/carriers', [CarrierController::class, 'store'])->name('carriers.store');
    Route::put('/carriers/{carrier}', [CarrierController::class, 'update'])->name('carriers.update');

    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');

    // Configuration: Currencies & Exchange Rates (Administrator only)
    Route::get('/currencies', [CurrencyController::class, 'index'])->name('currencies.index');
    Route::patch('/currencies/{currency}', [CurrencyController::class, 'updateRate'])->name('currencies.update-rate');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Audit Logs (Administrator only)
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
