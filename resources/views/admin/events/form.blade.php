@extends('layouts.admin')

@section('page_title', $event->exists ? 'Edit Event: ' . $event->title : 'Create an Entry: Event')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Strapi Form Top Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('strapi.events.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline flex items-center gap-1.5 mb-1">
                <i class="fas fa-arrow-left text-[10px]"></i>
                <span>Back to Events list</span>
            </a>
            <h2 class="font-sans font-semibold text-xl text-[#32324d]">
                {{ $event->exists ? 'Edit entry' : 'Create an entry' }}
            </h2>
        </div>

        @if ($event->exists)
            <a href="{{ route('events.show', ['event' => $event]) }}" target="_blank" class="text-xs font-medium text-[#666687] hover:text-[#4945ff] flex items-center gap-1.5">
                <span>View on Live Site</span>
                <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
            </a>
        @endif
    </div>

    <form
        method="POST"
        action="{{ $event->exists ? route('strapi.events.update', $event) : route('strapi.events.store') }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >
        @csrf
        @if ($event->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Main Form Card (8 cols) -->
            <div class="lg:col-span-8 bg-white p-6 rounded-xl border border-[#eaeaef] shadow-strapi space-y-5">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-medium text-[#32324d] mb-1.5">
                        Event Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        value="{{ old('title', $event->title) }}"
                        required
                        class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff] text-[#32324d]"
                        placeholder="e.g. Jakarta Film Week 2026"
                    >
                    @error('title')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type & City -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-xs font-medium text-[#32324d] mb-1.5">
                            Event Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" class="w-full border border-[#dcdce4] rounded-md px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]">
                            @foreach ($types as $t)
                                <option value="{{ $t }}" {{ old('type', $event->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="city" class="block text-xs font-medium text-[#32324d] mb-1.5">
                            Host City
                        </label>
                        <input
                            type="text"
                            name="city"
                            id="city"
                            value="{{ old('city', $event->city) }}"
                            class="w-full border border-[#dcdce4] rounded-md px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                            placeholder="e.g. Jakarta / Yogyakarta / Bali"
                        >
                    </div>
                </div>

                <!-- Schedule Dates -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="starts_at" class="block text-xs font-medium text-[#32324d] mb-1.5">
                            Start Date & Time <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            name="starts_at"
                            id="starts_at"
                            value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}"
                            required
                            class="w-full border border-[#dcdce4] rounded-md px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                        >
                    </div>
                    <div>
                        <label for="ends_at" class="block text-xs font-medium text-[#32324d] mb-1.5">
                            End Date & Time
                        </label>
                        <input
                            type="datetime-local"
                            name="ends_at"
                            id="ends_at"
                            value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}"
                            class="w-full border border-[#dcdce4] rounded-md px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                        >
                    </div>
                </div>

                <!-- Venue -->
                <div>
                    <label for="venue" class="block text-xs font-medium text-[#32324d] mb-1.5">
                        Venue / Physical Address
                    </label>
                    <input
                        type="text"
                        name="venue"
                        id="venue"
                        value="{{ old('venue', $event->venue) }}"
                        class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                        placeholder="e.g. Bioskop Misbar Usman Janatin / Taman Ismail Marzuki"
                    >
                </div>

                <!-- URL -->
                <div>
                    <label for="url" class="block text-xs font-medium text-[#32324d] mb-1.5">
                        External Link / Registration / Tickets
                    </label>
                    <input
                        type="url"
                        name="url"
                        id="url"
                        value="{{ old('url', $event->url) }}"
                        class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                        placeholder="https://..."
                    >
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-xs font-medium text-[#32324d] mb-1.5">
                        Event Description & Program Details
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="6"
                        class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                        placeholder="Write details about the screening schedule, curated films, and panel discussions..."
                    >{{ old('description', $event->description) }}</textarea>
                </div>
            </div>

            <!-- Sidebar Panel (4 cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Save & Status Box -->
                <div class="bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi space-y-4">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-[#666687]">Information</h3>

                    <div class="text-xs space-y-2 text-[#666687] pt-2 border-t border-[#eaeaef]">
                        <div class="flex justify-between">
                            <span>Status:</span>
                            <span class="font-medium text-[#32324d]">{{ $event->is_published ? 'Published' : 'Draft' }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[#eaeaef]">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                name="is_published"
                                value="1"
                                {{ old('is_published', $event->is_published) ? 'checked' : '' }}
                                class="rounded border-[#dcdce4] text-[#4945ff] focus:ring-[#4945ff]"
                            >
                            <span class="text-xs font-medium text-[#32324d]">Publish on Live Calendar</span>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-[#eaeaef] flex gap-2">
                        <button
                            type="submit"
                            class="flex-1 px-4 py-2 bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium rounded-lg transition shadow-sm text-center"
                        >
                            Save
                        </button>
                        <a
                            href="{{ route('strapi.events.index') }}"
                            class="px-3 py-2 bg-[#f6f6f9] hover:bg-[#eaeaef] text-[#666687] text-xs font-medium rounded-lg transition text-center"
                        >
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Strapi Poster Media Picker -->
                <div class="bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi space-y-3">
                    <x-strapi-media-picker
                        name="image"
                        label="Event Poster Media"
                        :value="old('image', $event->image)"
                        :imageUrl="$event->image_url"
                    />
                </div>

            </div>

        </div>
    </form>
</div>
@endsection
