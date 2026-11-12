@extends('layouts.admin')

@section('page_title', 'Content Manager: Categories')

@section('content')
<div class="space-y-6">

    <!-- Strapi Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="font-sans font-semibold text-lg text-[#32324d]">Categories</h2>
                <span class="text-xs text-[#8e8ea9] font-normal">({{ $categories->count() }} entries found)</span>
            </div>
            <p class="text-xs text-[#666687] font-normal mt-0.5">Manage editorial categories and section taxonomy for Articles.</p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('strapi.categories.create') }}"
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
                        <th class="p-3.5 pl-5">Name & Description</th>
                        <th class="p-3.5">Slug</th>
                        <th class="p-3.5">Color Tag</th>
                        <th class="p-3.5 text-center">Entries</th>
                        <th class="p-3.5 text-right pr-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eaeaef]">
                    @foreach ($categories as $cat)
                        <tr class="hover:bg-[#f6f6f9]/80 transition">
                            <td class="p-3.5 pl-5">
                                <a href="{{ route('strapi.categories.edit', $cat) }}" class="font-medium text-[#32324d] hover:text-[#4945ff]">
                                    {{ $cat->name }}
                                </a>
                                @if ($cat->description)
                                    <p class="text-[11px] text-[#8e8ea9] line-clamp-1 mt-0.5">{{ $cat->description }}</p>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono text-[11px] text-[#666687] whitespace-nowrap">
                                /category/{{ $cat->slug }}
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium text-white shadow-xs" style="background-color: {{ $cat->color ?? '#3A07BB' }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white/70"></span>
                                    {{ $cat->color ?? '#3A07BB' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-center font-medium text-[#32324d]">
                                {{ $cat->articles_count }}
                            </td>
                            <td class="p-3.5 text-right pr-5 space-x-2 whitespace-nowrap">
                                <a href="{{ route('strapi.categories.edit', $cat) }}" class="p-1 text-[#666687] hover:text-[#4945ff]" title="Edit Entry">
                                    <i class="far fa-edit"></i>
                                </a>
                                <a href="{{ route('categories.show', ['category' => $cat]) }}" target="_blank" class="p-1 text-[#8e8ea9] hover:text-[#32324d]" title="View Live">
                                    <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                </a>
                                <form method="POST" action="{{ route('strapi.categories.destroy', $cat) }}" class="inline" data-confirm data-confirm-title="Hapus kategori?" data-confirm-message="Kategori ini akan dihapus permanen." data-confirm-action="Hapus kategori">
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
