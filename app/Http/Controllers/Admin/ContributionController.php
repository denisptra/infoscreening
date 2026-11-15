<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContributionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $contributions = Contribution::query()
            ->when(array_key_exists($status, Contribution::STATUSES), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.contributions.index', compact('contributions', 'status'));
    }

    public function show(Contribution $contribution): View
    {
        return view('admin.contributions.show', compact('contribution'));
    }

    public function update(Request $request, Contribution $contribution): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Contribution::STATUSES))],
        ]);

        $contribution->update($data);

        return redirect()->route('strapi.contributions.show', $contribution)->with('status', 'Status kontribusi diperbarui.');
    }

    public function destroy(Contribution $contribution): RedirectResponse
    {
        $contribution->delete();

        return redirect()->route('strapi.contributions.index')->with('status', 'Kontribusi dihapus.');
    }
}
