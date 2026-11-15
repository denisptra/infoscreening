<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteAppearanceController extends Controller
{
    public function updateFooter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'footer_about' => ['required', 'string', 'max:1000'],
            'footer_copyright' => ['required', 'string', 'max:150'],
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('strapi.navigation.index')->with('status', 'Isi footer berhasil disimpan.');
    }
}
