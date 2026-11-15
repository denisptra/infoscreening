@extends('layouts.admin')

@section('page_title', $tag->exists ? 'Edit Tag: #' . $tag->name : 'Create an Entry: Tag')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('strapi.tags.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline flex items-center gap-1.5 mb-1">
                <i class="fas fa-arrow-left text-[10px]"></i>
                <span>Back to Tags list</span>
            </a>
            <h2 class="font-sans font-semibold text-xl text-[#32324d]">
                {{ $tag->exists ? 'Edit tag' : 'Create an entry' }}
            </h2>
        </div>
    </div>

    <form method="POST" action="{{ $tag->exists ? route('strapi.tags.update', $tag) : route('strapi.tags.store') }}" class="bg-white p-6 rounded-xl border border-[#eaeaef] shadow-strapi space-y-5">
        @csrf
        @if ($tag->exists)
            @method('PUT')
        @endif

        <div>
            <label for="name" class="block text-xs font-medium text-[#32324d] mb-1.5">
                Tag Name <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $tag->name) }}"
                required
                class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                placeholder="e.g. Festival Film Indonesia / Sundance"
            >
            @error('name')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="block text-xs font-medium text-[#32324d] mb-1.5">
                Slug (URL Key)
            </label>
            <input
                type="text"
                name="slug"
                id="slug"
                value="{{ old('slug', $tag->slug) }}"
                class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs font-mono text-[#666687] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                placeholder="tag-slug"
            >
            @error('slug')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-xs font-medium text-[#32324d] mb-1.5">
                Description
            </label>
            <textarea
                name="description"
                id="description"
                rows="3"
                class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                placeholder="Brief summary..."
            >{{ old('description', $tag->description) }}</textarea>
            @error('description')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#eaeaef]">
            <a href="{{ route('strapi.tags.index') }}" class="px-3.5 py-2 bg-[#f6f6f9] hover:bg-[#eaeaef] text-[#666687] text-xs font-medium rounded-lg transition">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium rounded-lg transition shadow-sm">
                Save
            </button>
        </div>
    </form>
</div>
@endsection
