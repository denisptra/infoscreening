@extends('layouts.admin')

@section('page_title', $expert->exists ? 'Edit Expert: ' . $expert->name : 'Create an Entry: Expert')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('strapi.experts.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline flex items-center gap-1.5 mb-1">
                <i class="fas fa-arrow-left text-[10px]"></i>
                <span>Back to Experts list</span>
            </a>
            <h2 class="font-sans font-semibold text-xl text-[#32324d]">
                {{ $expert->exists ? 'Edit expert' : 'Create an entry' }}
            </h2>
        </div>
    </div>

    <form
        method="POST"
        action="{{ $expert->exists ? route('strapi.experts.update', $expert) : route('strapi.experts.store') }}"
        enctype="multipart/form-data"
        class="bg-white p-6 rounded-xl border border-[#eaeaef] shadow-strapi space-y-5"
    >
        @csrf
        @if ($expert->exists)
            @method('PUT')
        @endif

        <div>
            <label for="name" class="block text-xs font-medium text-[#32324d] mb-1.5">
                Full Name &amp; Title <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $expert->name) }}"
                required
                class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                placeholder="e.g. Budi Laksono, M.Sn"
            >
            @error('name')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="specialty" class="block text-xs font-medium text-[#32324d] mb-1.5">
                    Specialty / Role <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="specialty"
                    id="specialty"
                    value="{{ old('specialty', $expert->specialty) }}"
                    required
                    class="w-full border border-[#dcdce4] rounded-md px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                    placeholder="e.g. Film Curator & Programming Consultant"
                >
            </div>
            <div>
                <label for="city" class="block text-xs font-medium text-[#32324d] mb-1.5">
                    City / Domicile
                </label>
                <input
                    type="text"
                    name="city"
                    id="city"
                    value="{{ old('city', $expert->city) }}"
                    class="w-full border border-[#dcdce4] rounded-md px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                    placeholder="Jakarta"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-xs font-medium text-[#32324d] mb-1.5">
                    Contact Email
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $expert->email) }}"
                    class="w-full border border-[#dcdce4] rounded-md px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                    placeholder="contact@example.com"
                >
            </div>
            <div>
                <label for="phone" class="block text-xs font-medium text-[#32324d] mb-1.5">
                    WhatsApp / Phone
                </label>
                <input
                    type="text"
                    name="phone"
                    id="phone"
                    value="{{ old('phone', $expert->phone) }}"
                    class="w-full border border-[#dcdce4] rounded-md px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                    placeholder="+62 8..."
                >
            </div>
        </div>

        <div>
            <x-strapi-media-picker
                name="photo"
                label="Profile Picture Asset"
                :value="old('photo', $expert->photo)"
                :imageUrl="$expert->photo_url"
            />
        </div>

        <div>
            <label for="bio" class="block text-xs font-medium text-[#32324d] mb-1.5">
                Biography &amp; Track Record
            </label>
            <textarea
                name="bio"
                id="bio"
                rows="4"
                class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
            >{{ old('bio', $expert->bio) }}</textarea>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    {{ old('is_published', $expert->is_published) ? 'checked' : '' }}
                    class="rounded border-[#dcdce4] text-[#4945ff] focus:ring-[#4945ff]"
                >
                <span class="text-xs font-medium text-[#32324d]">Show in Public Directory</span>
            </label>
        </div>

        <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#eaeaef]">
            <a href="{{ route('strapi.experts.index') }}" class="px-3.5 py-2 bg-[#f6f6f9] hover:bg-[#eaeaef] text-[#666687] text-xs font-medium rounded-lg transition">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium rounded-lg transition shadow-sm">
                Save
            </button>
        </div>
    </form>
</div>
@endsection
