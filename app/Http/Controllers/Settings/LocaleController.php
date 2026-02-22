<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'locale' => 'required|in:en,es',
        ]);

        $locale = $request->locale;

        session(['locale' => $locale]);

        if (Auth::check()) {
            $request->user()->update(['locale' => $locale]);
        }

        App::setLocale($locale);

        return back();
    }
}
