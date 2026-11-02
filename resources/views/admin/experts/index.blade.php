@extends('layouts.admin')

@section('page_title', 'Content Manager: Cinema Experts')

@section('content')
<div class="space-y-6">

    <!-- Strapi Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="font-sans font-semibold text-lg text-[#32324d]">Film Experts & Consultants</h2>
                <span class="text-xs text-[#8e8ea9] font-normal">({{ $experts->count() }} entries found)</span>
            </div>
            <p class="text-xs text-[#666687] font-normal mt-0.5">Curators, film archivists, workshop instructors, and production consultants.</p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('strapi.experts.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium transition shadow-sm whitespace-nowrap"
            >
                <i class="fas fa-plus text-[10px]"></i>
                <span>Create new entry</span>
            </a>
        </div>
    </div>

    <!-- Strapi Card Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($experts as $exp)
            <div class="bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi flex flex-col justify-between hover:border-[#4945ff]/40 transition">
                <div>
                    <div class="flex items-start gap-3.5">
                        <img loading="lazy" decoding="async"
                            src="{{ $exp->photo_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80' }}"
                            alt="{{ $exp->name }}"
                            class="w-12 h-12 rounded-full object-cover shrink-0 border border-slate-200"
                        >
                        <div class="min-w-0">
                            <h3 class="font-medium text-sm text-[#32324d] truncate">{{ $exp->name }}</h3>
                            <p class="text-xs text-[#4945ff] font-normal truncate">{{ $exp->specialty }}</p>
                            <span class="text-[10px] text-[#8e8ea9] block mt-0.5">{{ $exp->city ?? 'Indonesia' }}</span>
                        </div>
                    </div>

                    <p class="text-xs text-[#666687] mt-3 line-clamp-2 leading-relaxed">
                        {{ $exp->bio }}
                    </p>
                </div>

                <div class="flex items-center justify-between pt-4 mt-4 border-t border-[#eaeaef] text-xs">
                    <div>
                        @if ($exp->is_published)
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Published
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-normal text-slate-400">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span> Draft
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 whitespace-nowrap">
                        <a href="{{ route('strapi.experts.edit', $exp) }}" class="p-1 text-[#666687] hover:text-[#4945ff]" title="Edit Entry">
                            <i class="far fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('strapi.experts.destroy', $exp) }}" class="inline" onsubmit="return confirm('Delete this expert?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 text-[#8e8ea9] hover:text-red-600" title="Delete">
                                <i class="far fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-8 rounded-xl border border-[#eaeaef] text-center text-[#8e8ea9] text-xs">
                No expert entries found.
            </div>
        @endforelse
    </div>
</div>
@endsection
