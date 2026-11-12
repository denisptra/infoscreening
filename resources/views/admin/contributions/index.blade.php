@extends('layouts.admin')

@section('page_title', 'Content Manager: Submissions')

@section('content')
<div class="space-y-6">

    <!-- Strapi Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="font-sans font-semibold text-lg text-[#32324d]">Community Submissions</h2>
                <span class="text-xs text-[#8e8ea9] font-normal">({{ $contributions->total() }} entries found)</span>
            </div>
            <p class="text-xs text-[#666687] font-normal mt-0.5">Review articles, press releases, screening proposals, and festival programs submitted by readers.</p>
        </div>

        <div class="flex items-center gap-1.5 text-xs bg-[#f6f6f9] p-1 rounded-lg border border-[#eaeaef]">
            <a href="{{ route('strapi.contributions.index') }}" class="px-3 py-1 rounded-md transition font-medium {{ empty($status) ? 'bg-white text-[#4945ff] shadow-xs' : 'text-[#666687] hover:text-[#32324d]' }}">
                All
            </a>
            <a href="{{ route('strapi.contributions.index', ['status' => 'pending']) }}" class="px-3 py-1 rounded-md transition font-medium {{ $status === 'pending' ? 'bg-white text-[#4945ff] shadow-xs' : 'text-[#666687] hover:text-[#32324d]' }}">
                Pending
            </a>
            <a href="{{ route('strapi.contributions.index', ['status' => 'approved']) }}" class="px-3 py-1 rounded-md transition font-medium {{ $status === 'approved' ? 'bg-white text-[#4945ff] shadow-xs' : 'text-[#666687] hover:text-[#32324d]' }}">
                Approved
            </a>
            <a href="{{ route('strapi.contributions.index', ['status' => 'rejected']) }}" class="px-3 py-1 rounded-md transition font-medium {{ $status === 'rejected' ? 'bg-white text-[#4945ff] shadow-xs' : 'text-[#666687] hover:text-[#32324d]' }}">
                Rejected
            </a>
        </div>
    </div>

    <!-- Strapi Table View -->
    <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#f6f6f9] text-[#666687] font-semibold text-[11px] uppercase tracking-wider border-b border-[#eaeaef]">
                    <tr>
                        <th class="p-3.5 pl-5">Contributor</th>
                        <th class="p-3.5">Submission Details</th>
                        <th class="p-3.5">Received Date</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right pr-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eaeaef]">
                    @forelse ($contributions as $item)
                        <tr class="hover:bg-[#f6f6f9]/80 transition">
                            <td class="p-3.5 pl-5 whitespace-nowrap">
                                <span class="font-medium text-[#32324d] block">{{ $item->name }}</span>
                                <span class="text-[#8e8ea9] text-[11px]">{{ $item->email }}</span>
                            </td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-medium text-[#4945ff] bg-[#f0ebfc] px-2 py-0.5 rounded capitalize">
                                        {{ $item->type }}
                                    </span>
                                </div>
                                <a href="{{ route('strapi.contributions.show', $item) }}" class="font-medium text-[#32324d] hover:text-[#4945ff] block">
                                    {{ $item->title }}
                                </a>
                                <p class="text-[11px] text-[#8e8ea9] line-clamp-1 mt-0.5">{{ $item->message }}</p>
                            </td>
                            <td class="p-3.5 text-[#666687] whitespace-nowrap">
                                {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                @if ($item->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                                    </span>
                                @elseif ($item->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-rose-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-amber-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending Review
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right pr-5 whitespace-nowrap">
                                <a href="{{ route('strapi.contributions.show', $item) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#f6f6f9] hover:bg-[#4945ff] hover:text-white text-[#32324d] rounded-md font-medium text-xs transition border border-[#dcdce4]">
                                    <span>Review</span>
                                    <i class="fas fa-chevron-right text-[9px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-[#8e8ea9]">
                                No submission entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($contributions->hasPages())
            <div class="p-4 border-t border-[#eaeaef]">
                {{ $contributions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
