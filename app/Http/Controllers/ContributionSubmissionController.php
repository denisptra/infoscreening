<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContributionSubmissionController extends Controller
{
    public function create(): View
    {
        return view('contribute', ['types' => Contribution::TYPES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'type' => ['required', Rule::in(Contribution::TYPES)],
            'title' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            'link' => ['nullable', 'url', 'max:255'],
        ]);

        Contribution::create($data);

        return redirect()
            ->route('contribute.create')
            ->with('status', 'Terima kasih! Kontribusimu sudah kami terima dan akan segera ditinjau tim redaksi.');
    }
}
