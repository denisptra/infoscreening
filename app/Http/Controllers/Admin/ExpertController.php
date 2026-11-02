<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUploads;
use App\Http\Controllers\Controller;
use App\Models\Expert;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpertController extends Controller
{
    use HandlesImageUploads;

    public function index(): View
    {
        return view('admin.experts.index', ['experts' => Expert::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.experts.form', ['expert' => new Expert(['is_published' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['photo'] = $this->resolveImage($request, 'photo', null, 'experts');

        Expert::create($data);

        return redirect()->route('strapi.experts.index')->with('status', 'Tenaga ahli berhasil ditambahkan.');
    }

    public function edit(Expert $expert): View
    {
        return view('admin.experts.form', compact('expert'));
    }

    public function update(Request $request, Expert $expert): RedirectResponse
    {
        $data = $this->validated($request);
        $data['photo'] = $this->resolveImage($request, 'photo', $expert->photo, 'experts');

        $expert->update($data);

        return redirect()->route('strapi.experts.index')->with('status', 'Tenaga ahli berhasil diperbarui.');
    }

    public function destroy(Expert $expert): RedirectResponse
    {
        $this->deleteStoredImage($expert->photo);
        $expert->delete();

        return redirect()->route('strapi.experts.index')->with('status', 'Tenaga ahli dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'specialty' => ['required', 'string', 'max:160'],
            'city' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1500'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'direct_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'photo_url' => ['nullable', 'url', 'max:500'],
        ]);

        unset($data['photo'], $data['direct_photo'], $data['photo_url']);
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
