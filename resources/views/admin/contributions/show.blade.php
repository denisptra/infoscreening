@extends('layouts.admin')

@section('page_title', 'Review Submission: ' . $contribution->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('strapi.contributions.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline flex items-center gap-1.5 mb-1">
                <i class="fas fa-arrow-left text-[10px]"></i>
                <span>Back to Submissions list</span>
            </a>
            <h2 class="font-sans font-semibold text-xl text-[#32324d]">
                Review submission entry
            </h2>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-[#eaeaef] shadow-strapi space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-5 border-b border-[#eaeaef]">
            <div>
                <span class="text-[10px] font-medium text-[#4945ff] bg-[#f0ebfc] px-2.5 py-1 rounded capitalize">
                    {{ $contribution->type }}
                </span>
                <h3 class="font-sans font-semibold text-lg text-[#32324d] mt-2">{{ $contribution->title }}</h3>
            </div>
            <div>
                @if ($contribution->status === 'approved')
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                    </span>
                @elseif ($contribution->status === 'rejected')
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-rose-700 bg-rose-50 px-3 py-1 rounded-full border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Rejected
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending Review
                    </span>
                @endif
            </div>
        </div>

        <!-- Sender metadata -->
        <div class="bg-[#f6f6f9] p-4 rounded-xl border border-[#eaeaef] text-xs grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <span class="text-[#8e8ea9] block text-[11px] font-medium">Contributor Name:</span>
                <span class="font-medium text-[#32324d] text-sm">{{ $contribution->name }}</span>
            </div>
            <div>
                <span class="text-[#8e8ea9] block text-[11px] font-medium">Email Address:</span>
                <a href="mailto:{{ $contribution->email }}" class="text-[#4945ff] font-medium text-sm hover:underline">{{ $contribution->email }}</a>
            </div>
            <div>
                <span class="text-[#8e8ea9] block text-[11px] font-medium">Received At:</span>
                <span class="text-[#32324d]">{{ $contribution->created_at->translatedFormat('l, d F Y - H:i') }} WIB</span>
            </div>
            @if ($contribution->link)
                <div>
                    <span class="text-[#8e8ea9] block text-[11px] font-medium">External Document / Attachment Link:</span>
                    <a href="{{ $contribution->link }}" target="_blank" rel="noopener noreferrer" class="text-[#4945ff] font-medium hover:underline flex items-center gap-1 mt-0.5">
                        <span class="truncate max-w-xs">{{ $contribution->link }}</span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
            @endif
        </div>

        <!-- Message Body -->
        <div class="space-y-2">
            <h4 class="text-xs font-medium text-[#666687] uppercase tracking-wider">Submission Content & Remarks:</h4>
            <div class="bg-white border border-[#dcdce4] p-5 rounded-xl text-xs text-[#32324d] whitespace-pre-line leading-relaxed">
                {{ $contribution->message }}
            </div>
        </div>

        <!-- Status update & actions -->
        <div class="pt-5 border-t border-[#eaeaef] flex flex-wrap items-center justify-between gap-4">
            <form method="POST" action="{{ route('strapi.contributions.destroy', $contribution) }}" onsubmit="return confirm('Delete this submission?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 text-xs text-rose-600 hover:text-rose-800 font-medium rounded-lg hover:bg-rose-50 transition">
                    <i class="far fa-trash-can mr-1"></i> Delete Submission
                </button>
            </form>

            <form method="POST" action="{{ route('strapi.contributions.update', $contribution) }}" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <span class="text-xs text-[#666687] font-medium">Change Status:</span>
                <select name="status" class="text-xs border border-[#dcdce4] rounded-md px-3 py-1.5 text-[#32324d] focus:border-[#4945ff]">
                    <option value="pending" {{ $contribution->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $contribution->status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $contribution->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="px-4 py-1.5 bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium rounded-lg transition shadow-sm">
                    Save Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
