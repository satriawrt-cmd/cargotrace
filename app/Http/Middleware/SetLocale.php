<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale', 'id'));

        if (!in_array($locale, ['en', 'id'])) {
            $locale = 'id';
        }

        App::setLocale($locale);

        // Prepend localized view folder so Laravel loads either /locales/id or /locales/en first
        $localizedPath = resource_path("views/locales/{$locale}");
        if (is_dir($localizedPath)) {
            View::getFinder()->prependLocation($localizedPath);
        }

        return $next($request);
    }
}
