<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\Shipment;
use App\Policies\ShipmentPolicy;
use App\Services\CurrencyService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrencyService::class, function () {
            return new CurrencyService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::policy(Shipment::class, ShipmentPolicy::class);

        // Share currency & notification data across views
        View::composer('*', function ($view) {
            $currencyService = app(CurrencyService::class);
            $selectedCurrency = session('selected_currency', 'USD');
            $activeCurrencies = $currencyService->getActiveCurrencies();

            $unreadCount = 0;
            if (Auth::check()) {
                $unreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
            }

            $currentLocale = session('locale', config('app.locale', 'id'));

            $view->with([
                'globalCurrencies' => $activeCurrencies,
                'activeCurrencyCode' => $selectedCurrency,
                'currencyService' => $currencyService,
                'unreadNotificationsCount' => $unreadCount,
                'currentLocale' => $currentLocale,
            ]);
        });
    }
}
