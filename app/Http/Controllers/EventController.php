<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->string('jenis')->toString();
        $showPast = $request->get('waktu') === 'lalu';

        $events = Event::query()
            ->published()
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->when(
                $showPast,
                fn ($query) => $query->where('starts_at', '<', now()->startOfDay())->orderByDesc('starts_at'),
                fn ($query) => $query->upcoming()->orderBy('starts_at'),
            )
            ->paginate(9)
            ->withQueryString();

        return view('events.index', [
            'events' => $events,
            'types' => Event::TYPES,
            'activeType' => $type,
            'showPast' => $showPast,
        ]);
    }

    public function show(Event $event): View
    {
        abort_unless($event->is_published, 404);

        $others = Event::query()
            ->published()
            ->upcoming()
            ->whereKeyNot($event->id)
            ->orderBy('starts_at')
            ->limit(4)
            ->get();

        return view('events.show', compact('event', 'others'));
    }
}
