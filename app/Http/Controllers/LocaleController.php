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
        $validated = $request->validate([
            'locale' => 'required|in:en,ru',
        ]);

        // Update session locale
        $request->session()->put('locale', $locale);

        // If user is authenticated, update their locale in database
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        return back()->with('success', 'Language changed successfully!');
    }
}
