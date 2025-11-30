<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Change user's locale/language preference
     */
    public function change(Request $request, $locale)
    {
        // Validate locale parameter
        if (!in_array($locale, ['en', 'ru'])) {
            return back()->withErrors(['locale' => 'Invalid language selected']);
        }

        // Update session locale
        $request->session()->put('locale', $locale);

        // If user is authenticated, update their locale in database
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        return back();
    }
}
