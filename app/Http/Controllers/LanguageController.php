<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Switch application language between English and Indonesian.
     */
    public function switchLanguage(Request $request, ?string $locale = null)
    {
        $targetLocale = $locale ?? $request->input('locale');

        if (!in_array($targetLocale, ['en', 'id'])) {
            $targetLocale = 'en';
        }

        session(['locale' => $targetLocale]);
        App::setLocale($targetLocale);

        $message = $targetLocale === 'id' 
            ? 'Bahasa berhasil diubah ke Bahasa Indonesia.' 
            : 'Language successfully switched to English.';

        return back()->with('success', $message);
    }
}
