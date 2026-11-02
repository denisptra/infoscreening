<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index(): View
    {
        $users = User::all();

        $apiTokens = session('strapi_api_tokens', [
            [
                'id' => 1,
                'name' => 'Frontend Jamstack Read-Only',
                'description' => 'Digunakan oleh static site generator untuk fetch artikel dan kalender.',
                'type' => 'read-only',
                'token' => 'str_tok_'.substr(hash('sha256', 'infoscreening-read-key'), 0, 32),
                'created_at' => now()->subMonths(2)->format('Y-m-d'),
                'expires_at' => 'Never',
            ],
            [
                'id' => 2,
                'name' => 'Mobile App Integration',
                'description' => 'Akses read artikel dan kirim submissions berkontribusi.',
                'type' => 'custom',
                'token' => 'str_tok_'.substr(hash('sha256', 'infoscreening-mobile-key'), 0, 32),
                'created_at' => now()->subWeeks(3)->format('Y-m-d'),
                'expires_at' => '2027-12-31',
            ],
        ]);

        $webhooks = [
            [
                'id' => 1,
                'name' => 'Vercel / Netlify Deploy Hook',
                'url' => 'https://api.vercel.com/v1/integrations/deploy/prj_infoscreening/trigger',
                'events' => ['entry.create', 'entry.update', 'entry.publish'],
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Telegram Editorial Alert',
                'url' => 'https://api.telegram.org/bot/notify-submission',
                'events' => ['submission.pending'],
                'is_active' => true,
            ],
        ];

        return view('admin.settings.index', [
            'users' => $users,
            'apiTokens' => $apiTokens,
            'webhooks' => $webhooks,
            'activeTab' => request('tab', 'general'),
        ]);
    }

    public function createToken(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:read-only,full-access,custom'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $tokens = session('strapi_api_tokens', []);

        $newToken = [
            'id' => count($tokens) + 10,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? 'Token API kustom',
            'type' => $validated['type'],
            'token' => 'str_tok_'.Str::random(32),
            'created_at' => now()->format('Y-m-d'),
            'expires_at' => 'Never',
        ];

        $tokens[] = $newToken;
        session(['strapi_api_tokens' => $tokens]);

        return redirect()->route('strapi.settings.index', ['tab' => 'tokens'])
            ->with('status', 'API Token berhasil dibuat! Simpan token ini dengan aman.');
    }
}
