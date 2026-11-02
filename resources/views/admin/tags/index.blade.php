@extends('layouts.admin')

@section('page_title', 'Content Manager: Tags')

@section('content')
<div class="space-y-6">

    <!-- Strapi Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="font-sans font-semibold text-lg text-[#32324d]">Tags & Keywords</h2>
                <span class="text-xs text-[#8e8ea9] font-normal">({{ $tags->count() }} entries found)</span>
            </div>
            <p class="text-xs text-[#666687] font-normal mt-0.5">Manage topic tags, keywords, and festivals connected to stories.</p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('strapi.tags.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium transition shadow-sm whitespace-nowrap"
            >
                <i class="fas fa-plus text-[10px]"></i>
                <span>Create new entry</span>
            </a>
        </div>
    </div>

    <!-- Strapi Table View -->
    <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#f6f6f9] text-[#666687] font-semibold text-[11px] uppercase tracking-wider border-b border-[#eaeaef]">
                    <tr>
                        <th class="p-3.5 pl-5">Tag Name</th>
                        <th class="p-3.5">Slug</th>
                        <th class="p-3.5 text-center">Entries Attached</th>
                        <th class="p-3.5 text-right pr-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eaeaef]">
                    @foreach ($tags as $tag)
                        <tr class="hover:bg-[#f6f6f9]/80 transition">
                            <td class="p-3.5 pl-5">
                                <a href="{{ route('strapi.tags.edit', $tag) }}" class="font-medium text-[#32324d] hover:text-[#4945ff]">
                                    #{{ $tag->name }}
                                </a>
                            </td>
                            <td class="p-3.5 font-mono text-[11px] text-[#666687] whitespace-nowrap">
                                /tag/{{ $tag->slug }}
                            </td>
                            <td class="p-3.5 text-center font-medium text-[#32324d]">
                                {{ $tag->articles_count }}
                            </td>
                            <td class="p-3.5 text-right pr-5 space-x-2 whitespace-nowrap">
                                <a href="{{ route('strapi.tags.edit', $tag) }}" class="p-1 text-[#666687] hover:text-[#4945ff]" title="Edit Entry">
                                    <i class="far fa-edit"></i>
                                </a>
                                <a href="{{ route('tags.show', ['tag' => $tag]) }}" target="_blank" class="p-1 text-[#8e8ea9] hover:text-[#32324d]" title="View Live">
                                    <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                </a>
                                <form method="POST" action="{{ route('strapi.tags.destroy', $tag) }}" class="inline" onsubmit="return confirm('Delete this tag?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-[#8e8ea9] hover:text-red-600" title="Delete">
                                        <i class="far fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
