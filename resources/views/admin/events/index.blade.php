@extends('layouts.admin')

@section('page_title', 'Content Manager: Cinema Events')

@section('content')
<div class="space-y-6">

    <!-- Strapi Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="font-sans font-semibold text-lg text-[#32324d]">Events & Screenings</h2>
                <span class="text-xs text-[#8e8ea9] font-normal">({{ $events->total() }} entries found)</span>
            </div>
            <p class="text-xs text-[#666687] font-normal mt-0.5">Manage film festivals, community screenings, awards, and workshops.</p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('strapi.events.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium transition shadow-sm whitespace-nowrap"
            >
                <i class="fas fa-plus text-[10px]"></i>
                <span>Create new entry</span>
            </a>
        </div>
    </div>

    <!-- Strapi Filter & Search Bar -->
    <div class="bg-white p-3.5 rounded-xl border border-[#eaeaef] shadow-strapi flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2 text-xs text-[#666687]">
            <i class="fas fa-filter text-[#8e8ea9]"></i>
            <span>Calendar Collections</span>
        </div>
        <span class="text-[11px] text-[#8e8ea9]">
            Showing {{ $events->count() }} of {{ $events->total() }} entries
        </span>
    </div>

    <!-- Strapi Table View -->
    <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#f6f6f9] text-[#666687] font-semibold text-[11px] uppercase tracking-wider border-b border-[#eaeaef]">
                    <tr>
                        <th class="p-3.5 pl-5">Event Title</th>
                        <th class="p-3.5">Type</th>
                        <th class="p-3.5">Date & Schedule</th>
                        <th class="p-3.5">Location</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right pr-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eaeaef]">
                    @forelse ($events as $ev)
                        <tr class="hover:bg-[#f6f6f9]/80 transition">
                            <td class="p-3.5 pl-5">
                                <div class="flex items-center gap-3">
                                    <img loading="lazy" decoding="async" src="{{ $ev->image_url ?? 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=400&q=80' }}" class="w-10 h-10 rounded-lg object-cover shrink-0 border border-slate-200">
                                    <div class="min-w-0">
                                        <a href="{{ route('strapi.events.edit', $ev) }}" class="font-medium text-[#32324d] hover:text-[#4945ff] line-clamp-1">
                                            {{ $ev->title }}
                                        </a>
                                        <p class="text-[10px] text-[#8e8ea9] truncate mt-0.5">
                                            {{ $ev->organizer ?? 'Infoscreening Network' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="text-[10px] font-medium text-[#4945ff] bg-[#f0ebfc] px-2 py-0.5 rounded capitalize">
                                    {{ $ev->type }}
                                </span>
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                <div class="font-medium text-[#32324d]">{{ $ev->starts_at->translatedFormat('d M Y') }}</div>
                                <div class="text-[10px] text-[#8e8ea9]">{{ $ev->starts_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="p-3.5 text-[#666687] whitespace-nowrap">
                                <div class="truncate max-w-[200px]">{{ $ev->venue ?? 'Online' }}</div>
                                <div class="text-[10px] text-[#8e8ea9]">{{ $ev->city ?? '-' }}</div>
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                @if ($ev->is_published)
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-normal text-slate-400">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span> Draft
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right pr-5 space-x-2 whitespace-nowrap">
                                <a href="{{ route('strapi.events.edit', $ev) }}" class="p-1 text-[#666687] hover:text-[#4945ff]" title="Edit Entry">
                                    <i class="far fa-edit"></i>
                                </a>
                                <a href="{{ route('events.show', ['event' => $ev]) }}" target="_blank" class="p-1 text-[#8e8ea9] hover:text-[#32324d]" title="View Live">
                                    <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                </a>
                                <form method="POST" action="{{ route('strapi.events.destroy', $ev) }}" class="inline" onsubmit="return confirm('Delete this event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-[#8e8ea9] hover:text-red-600" title="Delete">
                                        <i class="far fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#8e8ea9]">
                                No event entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($events->hasPages())
            <div class="p-4 border-t border-[#eaeaef]">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
