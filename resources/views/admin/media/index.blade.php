@extends('layouts.admin')

@section('page_title', 'Media Library (Upload)')

@section('content')
<div class="space-y-6" x-data="strapiMediaLibrary()">

    <!-- Strapi Media Library Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="font-sans font-semibold text-lg text-[#32324d]">Media Library</h2>
                <span class="text-xs text-[#8e8ea9] font-normal">({{ $totalCount }} assets found)</span>
            </div>
            <p class="text-xs text-[#666687] font-normal mt-0.5">Manage and upload cinema posters, film stills, headshots, and media assets.</p>
        </div>

        <div class="flex items-center gap-3">
            <button
                type="button"
                @click="openUploadModal = true"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium transition shadow-sm whitespace-nowrap"
            >
                <i class="fas fa-plus text-[10px]"></i>
                <span>Add new assets</span>
            </button>
        </div>
    </div>

    <!-- Strapi Media Filters & Search Bar -->
    <div class="bg-white p-3.5 rounded-xl border border-[#eaeaef] shadow-strapi flex flex-wrap items-center justify-between gap-3 text-xs">
        <form method="GET" action="{{ route('strapi.media.index') }}" class="flex items-center gap-2 max-w-md w-full">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-2.5 text-[#8e8ea9] text-xs"></i>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search by asset name..."
                    class="w-full pl-8 pr-3 py-1.5 text-xs border border-[#dcdce4] rounded-md focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff] text-[#32324d]"
                >
            </div>
            <label class="relative block">
                <span class="sr-only">Filter tipe aset</span>
                <select
                    name="type"
                    onchange="this.form.submit()"
                    aria-label="Filter tipe aset"
                    class="min-h-10 appearance-none rounded-full border-2 border-[#2f62e8] bg-white py-2 pl-4 pr-10 text-sm font-medium text-[#32324d] shadow-sm outline-none transition focus:ring-4 focus:ring-[#2f62e8]/15"
                >
                    <option value="">All asset types</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images only</option>
                    <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Videos</option>
                    <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>Documents</option>
                </select>
                <i class="fas fa-chevron-down pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-[#2f62e8]" aria-hidden="true"></i>
            </label>
            <button type="submit" class="px-3 py-1.5 bg-[#f6f6f9] hover:bg-[#eaeaef] text-[#32324d] font-medium rounded-md transition">
                Filter
            </button>
        </form>

        <div class="flex items-center gap-4 text-[11px] text-[#8e8ea9]">
            <span>Storage: <strong class="text-[#32324d] font-medium">{{ number_format($totalBytes / 1048576, 2) }} MB</strong> used</span>
            <span>&bull;</span>
            <span>Showing {{ $media->count() }} of {{ $totalCount }} assets</span>
        </div>
    </div>

    <!-- Strapi Media Asset Grid -->
    <div class="bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi">
        @if ($media->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach ($media as $item)
                    <div class="group relative bg-[#f6f6f9] rounded-lg border border-[#eaeaef] overflow-hidden hover:border-[#4945ff] transition flex flex-col justify-between">
                        
                        <!-- Thumbnail Preview -->
                        <div class="aspect-square w-full relative bg-[#181826] overflow-hidden flex items-center justify-center">
                            @if ($item->isImage())
                                <img
                                    src="{{ $item->url }}"
                                    alt="{{ $item->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                    loading="lazy" decoding="async"
                                >
                            @else
                                <i class="far fa-file text-3xl text-slate-400"></i>
                            @endif

                            <!-- Format Badge -->
                            <span class="absolute top-1.5 left-1.5 bg-black/70 backdrop-blur-xs text-white text-[9px] font-mono px-1.5 py-0.5 rounded uppercase">
                                {{ pathinfo($item->name, PATHINFO_EXTENSION) ?: 'IMG' }}
                            </span>

                            <!-- Hover Overlay with Copy & View Actions -->
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                <button
                                    type="button"
                                    @click="copyUrl('{{ $item->url }}')"
                                    class="p-2 bg-white text-[#32324d] hover:text-[#4945ff] rounded-md text-xs shadow-sm"
                                    title="Copy asset URL"
                                >
                                    <i class="far fa-copy"></i>
                                </button>
                                <a
                                    href="{{ $item->url }}"
                                    target="_blank"
                                    class="p-2 bg-white text-[#32324d] hover:text-[#4945ff] rounded-md text-xs shadow-sm"
                                    title="Open asset"
                                >
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                </a>
                                <form method="POST" action="{{ route('strapi.media.destroy', $item) }}" class="inline" data-confirm data-confirm-title="Hapus aset?" data-confirm-message="Aset ini akan dihapus dari Media Library dan tidak bisa dipulihkan." data-confirm-action="Hapus aset">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="p-2 bg-white text-rose-600 hover:text-rose-700 rounded-md text-xs shadow-sm"
                                        title="Delete asset"
                                    >
                                        <i class="far fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Card Metadata -->
                        <div class="p-2.5 space-y-0.5 bg-white border-t border-[#eaeaef]">
                            <p class="text-xs font-medium text-[#32324d] truncate" title="{{ $item->name }}">
                                {{ $item->name }}
                            </p>
                            <div class="flex items-center justify-between text-[10px] text-[#8e8ea9]">
                                <span>{{ $item->formatted_size }}</span>
                                @if ($item->width && $item->height)
                                    <span>{{ $item->width }}&times;{{ $item->height }}</span>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            @if ($media->hasPages())
                <div class="pt-5 border-t border-[#eaeaef] mt-5">
                    {{ $media->links() }}
                </div>
            @endif
        @else
            <!-- Empty State Strapi Style -->
            <div class="py-16 text-center space-y-3">
                <div class="w-14 h-14 rounded-full bg-[#f0ebfc] text-[#4945ff] flex items-center justify-center text-xl mx-auto">
                    <i class="fas fa-cloud-arrow-up"></i>
                </div>
                <h3 class="font-sans font-semibold text-sm text-[#32324d]">No assets found in Media Library</h3>
                <p class="text-xs text-[#8e8ea9] max-w-sm mx-auto font-normal">
                    Drag and drop image files or click the button below to upload your first asset.
                </p>
                <div class="pt-2">
                    <button
                        type="button"
                        @click="openUploadModal = true"
                        class="px-4 py-2 bg-[#4945ff] text-white text-xs font-medium rounded-lg hover:bg-[#3b37eb] transition shadow-sm"
                    >
                        Upload an asset
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Strapi Upload Modal -->
    <div
        x-show="openUploadModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-[#212134]/60 backdrop-blur-xs"
        @click.self="requestCloseUpload()"
        @keydown.escape.window="requestCloseUpload()"
    >
        <section role="dialog" aria-modal="true" aria-labelledby="media-upload-title" class="bg-white rounded-xl shadow-2xl border border-[#eaeaef] max-w-xl w-full overflow-hidden">
            
            <!-- Modal Header -->
            <div class="p-4 px-6 border-b border-[#eaeaef] flex items-center justify-between bg-white">
                <div>
                    <h3 id="media-upload-title" class="font-sans font-semibold text-sm text-[#32324d]">Add new assets</h3>
                    <p class="text-[11px] text-[#8e8ea9]">Upload from your computer or import from URL</p>
                </div>
                <button type="button" @click="requestCloseUpload()" aria-label="Tutup upload" class="text-[#8e8ea9] hover:text-[#32324d]">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Upload Tabs -->
            <div class="border-b border-[#eaeaef] px-6 bg-[#f6f6f9] flex gap-6 text-xs font-medium">
                <button
                    type="button"
                    @click="uploadTab = 'computer'"
                    class="py-3 border-b-2 transition"
                    :class="uploadTab === 'computer' ? 'border-[#4945ff] text-[#4945ff]' : 'border-transparent text-[#666687] hover:text-[#32324d]'"
                >
                    <i class="fas fa-laptop mr-1.5"></i> From computer
                </button>
                <button
                    type="button"
                    @click="uploadTab = 'url'"
                    class="py-3 border-b-2 transition"
                    :class="uploadTab === 'url' ? 'border-[#4945ff] text-[#4945ff]' : 'border-transparent text-[#666687] hover:text-[#32324d]'"
                >
                    <i class="fas fa-link mr-1.5"></i> From URL
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6">
                
                <!-- Tab: From Computer -->
                <div x-show="uploadTab === 'computer'">
                    <form method="POST" action="{{ route('strapi.media.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <!-- Strapi Drag & Drop Zone -->
                        <div
                            class="border-2 border-dashed border-[#dcdce4] hover:border-[#4945ff] rounded-xl p-8 text-center bg-[#f6f6f9]/60 transition cursor-pointer"
                            @dragover.prevent
                            @drop.prevent="handleDrop($event)"
                            @click="$refs.fileInput.click()"
                        >
                            <input
                                type="file"
                                name="files[]"
                                multiple
                                accept="image/*,video/*,application/pdf"
                                class="hidden"
                                x-ref="fileInput"
                                @change="handleFiles($event.target.files)"
                            >
                            <div class="w-12 h-12 rounded-full bg-[#f0ebfc] text-[#4945ff] flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-cloud-arrow-up text-lg"></i>
                            </div>
                            <p class="text-xs font-medium text-[#32324d]">
                                Drag &amp; drop files here or <span class="text-[#4945ff] underline">browse files</span>
                            </p>
                            <p class="text-[11px] text-[#8e8ea9] mt-1">PNG, JPG, WEBP, GIF, MP4, PDF up to 20MB</p>
                        </div>

                        <!-- Selected Files Preview List -->
                        <template x-if="selectedFiles.length > 0">
                            <div class="space-y-2 max-h-48 overflow-y-auto p-1">
                                <template x-for="(f, idx) in selectedFiles" :key="idx">
                                    <div class="flex items-center justify-between p-2.5 bg-[#f6f6f9] rounded-lg border border-[#eaeaef] text-xs">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <i class="far fa-file-image text-[#4945ff]"></i>
                                            <span class="truncate font-medium text-[#32324d]" x-text="f.name"></span>
                                            <span class="text-[10px] text-[#8e8ea9]" x-text="formatSize(f.size)"></span>
                                        </div>
                                        <button type="button" @click="removeFile(idx)" class="text-rose-500 hover:text-rose-700 ml-2">
                                            <i class="far fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Modal Actions -->
                        <div class="pt-4 border-t border-[#eaeaef] flex items-center justify-end gap-2">
                            <button
                                type="button"
                                @click="requestCloseUpload()"
                                class="px-3.5 py-2 bg-[#f6f6f9] hover:bg-[#eaeaef] text-[#666687] text-xs font-medium rounded-lg transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="selectedFiles.length === 0"
                                class="px-4 py-2 bg-[#4945ff] hover:bg-[#3b37eb] disabled:opacity-50 text-white text-xs font-medium rounded-lg transition shadow-sm"
                            >
                                Upload <span x-text="selectedFiles.length"></span> asset(s) to library
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab: From URL -->
                <div x-show="uploadTab === 'url'">
                    <form method="POST" action="{{ route('strapi.media.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-[#32324d] mb-1.5">Asset URL</label>
                            <input
                                type="url"
                                name="url"
                                required
                                placeholder="https://example.com/poster.jpg"
                                class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#32324d] mb-1.5">Caption (Optional)</label>
                            <input
                                type="text"
                                name="caption"
                                placeholder="Poster Festival Sinema"
                                class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                            >
                        </div>

                        <!-- Modal Actions -->
                        <div class="pt-4 border-t border-[#eaeaef] flex items-center justify-end gap-2">
                            <button
                                type="button"
                                @click="requestCloseUpload()"
                                class="px-3.5 py-2 bg-[#f6f6f9] hover:bg-[#eaeaef] text-[#666687] text-xs font-medium rounded-lg transition"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium rounded-lg transition shadow-sm"
                            >
                                Add asset to library
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </section>
    </div>

</div>

<script>
function strapiMediaLibrary() {
    return {
        openUploadModal: false,
        uploadTab: 'computer',
        selectedFiles: [],
        handleFiles(files) {
            this.selectedFiles = Array.from(files);
        },

        handleDrop(event) {
            const dt = event.dataTransfer;
            if (dt && dt.files && dt.files.length) {
                this.selectedFiles = Array.from(dt.files);
                this.$refs.fileInput.files = dt.files;
            }
        },

        removeFile(idx) {
            this.selectedFiles.splice(idx, 1);
        },

        requestCloseUpload() {
            if (this.selectedFiles.length === 0) {
                this.openUploadModal = false;
                return;
            }

            window.dispatchEvent(new CustomEvent('app-confirm', {
                detail: {
                    title: 'Buang file yang dipilih?',
                    message: 'File yang sudah dipilih belum diunggah dan akan dihapus dari daftar.',
                    action: 'Buang file',
                    onConfirm: () => {
                        this.selectedFiles = [];
                        this.$refs.fileInput.value = '';
                        this.openUploadModal = false;
                    },
                },
            }));
        },

        formatSize(bytes) {
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(0) + ' KB';
            return bytes + ' B';
        },

        copyUrl(url) {
            window.dispatchEvent(new CustomEvent('app-copy', {
                detail: { text: url, label: 'URL aset' },
            }));
        }
    };
}
</script>
@endsection
