<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUploads;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    use HandlesImageUploads;

    public function index(Request $request): View
    {
        $search = trim((string) $request->get('q', ''));

        $events = Event::query()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->orderByDesc('starts_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.events.index', compact('events', 'search'));
    }

    public function create(): View
    {
        return view('admin.events.form', [
            'event' => new Event(['is_published' => true, 'type' => 'Pemutaran', 'starts_at' => now()->addWeek()->setTime(19, 0)]),
            'types' => Event::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image'] = $this->resolveImage($request, 'image', null, 'events');

        Event::create($data);

        return redirect()->route('strapi.events.index')->with('status', 'Event berhasil dibuat.');
    }

    public function edit(Event $event): View
    {
        return view('admin.events.form', ['event' => $event, 'types' => Event::TYPES]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image'] = $this->resolveImage($request, 'image', $event->image, 'events');

        $event->update($data);

        return redirect()->route('strapi.events.index')->with('status', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->deleteStoredImage($event->image);
        $event->delete();

        return redirect()->route('strapi.events.index')->with('status', 'Event dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'type' => ['required', Rule::in(Event::TYPES)],
            'description' => ['nullable', 'string'],
            'venue' => ['nullable', 'string', 'max:160'],
            'city' => ['nullable', 'string', 'max:100'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'url' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'direct_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ]);

        unset($data['image'], $data['direct_image'], $data['image_url']);
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
