<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => 'required|in:en,es',
        ]);

        $locale = $validated['locale'];
        $user = $request->user();

        if ($user === null) {
            return back();
        }

        session(['locale' => $locale]);

        $user->fill(['locale' => $locale])->save();

        App::setLocale($locale);

        return back();
    }
}
