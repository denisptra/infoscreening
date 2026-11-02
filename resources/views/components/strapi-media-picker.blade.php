@props([
    'name' => 'featured_image',
    'label' => 'Asset (Media)',
    'value' => null,
    'imageUrl' => null,
])

<div
    x-data="strapiAssetPicker({
        fieldName: '{{ $name }}',
        initialValue: '{{ $value ?? '' }}',
        initialUrl: '{{ $imageUrl ?? '' }}'
    })"
    class="space-y-2"
>
    <div class="flex items-center justify-between">
        <label class="block text-xs font-medium text-[#32324d]">{{ $label }}</label>
        <template x-if="currentUrl">
            <button
                type="button"
                @click="clearAsset()"
                class="text-[11px] text-rose-600 hover:underline flex items-center gap-1"
            >
                <i class="far fa-trash-can text-[10px]"></i>
                <span>Remove asset</span>
            </button>
        </template>
    </div>

    <!-- Hidden input that carries the selected asset value (storage path or URL) -->
    <input type="hidden" :name="fieldName" :value="currentValue">
    <input type="hidden" :name="fieldName + '_url'" :value="currentUrl">

    <!-- Selected Asset Preview (Strapi style) -->
    <template x-if="currentUrl">
        <div class="relative rounded-xl border border-[#eaeaef] bg-[#f6f6f9] p-3 space-y-2 group">
            <div class="aspect-video w-full rounded-lg overflow-hidden bg-[#181826] relative flex items-center justify-center">
                <img loading="lazy" decoding="async" :src="currentUrl" alt="Selected media" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                    <button
                        type="button"
                        @click="openModal = true; loadLibrary()"
                        class="px-3 py-1.5 bg-white text-[#32324d] hover:text-[#4945ff] rounded-md text-xs font-medium shadow-sm flex items-center gap-1.5"
                    >
                        <i class="fas fa-arrows-rotate text-[10px]"></i>
                        <span>Replace asset</span>
                    </button>
                    <a
                        :href="currentUrl"
                        target="_blank"
                        class="p-2 bg-white text-[#32324d] hover:text-[#4945ff] rounded-md text-xs shadow-sm"
                        title="View Full Size"
                    >
                        <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                    </a>
                </div>
            </div>
            <div class="flex items-center justify-between text-[11px] text-[#666687] px-1">
                <span class="truncate max-w-[200px]" x-text="currentName || 'Selected Media'"></span>
                <span class="text-emerald-600 font-medium flex items-center gap-1">
                    <i class="fas fa-check-circle text-[10px]"></i> Attached
                </span>
            </div>
        </div>
    </template>

    <!-- Empty Dropzone (Strapi style) -->
    <template x-if="!currentUrl">
        <div
            @click="openModal = true; loadLibrary()"
            class="border-2 border-dashed border-[#dcdce4] hover:border-[#4945ff] rounded-xl p-6 text-center bg-[#f6f6f9]/60 hover:bg-[#f0ebfc]/30 transition cursor-pointer group"
        >
            <div class="w-10 h-10 rounded-full bg-[#f0ebfc] text-[#4945ff] flex items-center justify-center mx-auto mb-2 group-hover:scale-105 transition">
                <i class="fas fa-plus text-xs"></i>
            </div>
            <p class="text-xs font-medium text-[#32324d]">
                Click to add an asset or upload from computer
            </p>
            <p class="text-[11px] text-[#8e8ea9] mt-0.5">
                PNG, JPG, WEBP, GIF from Strapi Media Library
            </p>
        </div>
    </template>

    <!-- Fallback Direct File Upload Option -->
    <div class="pt-1 flex items-center justify-between text-[11px] text-[#8e8ea9]">
        <span>Or upload directly:</span>
        <label class="cursor-pointer text-[#4945ff] hover:underline font-medium">
            <span>Browse computer file</span>
            <input
                type="file"
                :name="'direct_' + fieldName"
                accept="image/*"
                class="hidden"
                @change="handleDirectFile($event.target.files[0])"
            >
        </label>
    </div>

    <!-- Strapi Asset Picker Dialog Modal -->
    <div
        x-show="openModal"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-[#212134]/60 backdrop-blur-xs"
        @keydown.escape.window="openModal = false"
    >
        <div class="bg-white rounded-xl shadow-2xl border border-[#eaeaef] max-w-3xl w-full max-h-[85vh] flex flex-col overflow-hidden" @click.away="openModal = false">
            
            <!-- Modal Header -->
            <div class="p-4 px-6 border-b border-[#eaeaef] flex items-center justify-between bg-white shrink-0">
                <div>
                    <h3 class="font-sans font-semibold text-sm text-[#32324d]">Select or upload an asset</h3>
                    <p class="text-[11px] text-[#8e8ea9]">Choose from existing Media Library or upload new image</p>
                </div>
                <button type="button" @click="openModal = false" class="text-[#8e8ea9] hover:text-[#32324d]">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Modal Navigation Tabs -->
            <div class="border-b border-[#eaeaef] px-6 bg-[#f6f6f9] flex gap-6 text-xs font-medium shrink-0">
                <button
                    type="button"
                    @click="activeTab = 'library'"
                    class="py-3 border-b-2 transition"
                    :class="activeTab === 'library' ? 'border-[#4945ff] text-[#4945ff]' : 'border-transparent text-[#666687] hover:text-[#32324d]'"
                >
                    <i class="fas fa-images mr-1.5"></i> Media Library
                </button>
                <button
                    type="button"
                    @click="activeTab = 'upload'"
                    class="py-3 border-b-2 transition"
                    :class="activeTab === 'upload' ? 'border-[#4945ff] text-[#4945ff]' : 'border-transparent text-[#666687] hover:text-[#32324d]'"
                >
                    <i class="fas fa-cloud-arrow-up mr-1.5"></i> Upload from computer
                </button>
                <button
                    type="button"
                    @click="activeTab = 'url'"
                    class="py-3 border-b-2 transition"
                    :class="activeTab === 'url' ? 'border-[#4945ff] text-[#4945ff]' : 'border-transparent text-[#666687] hover:text-[#32324d]'"
                >
                    <i class="fas fa-link mr-1.5"></i> From URL
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto flex-1">
                
                <!-- Tab 1: Media Library Grid -->
                <div x-show="activeTab === 'library'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="relative w-64">
                            <i class="fas fa-search absolute left-3 top-2.5 text-[#8e8ea9] text-xs"></i>
                            <input
                                type="text"
                                x-model="searchQuery"
                                @input.debounce.300ms="loadLibrary()"
                                placeholder="Search library..."
                                class="w-full pl-8 pr-3 py-1.5 text-xs border border-[#dcdce4] rounded-md focus:border-[#4945ff] text-[#32324d]"
                            >
                        </div>
                        <span class="text-[11px] text-[#8e8ea9]" x-text="libraryAssets.length + ' assets available'"></span>
                    </div>

                    <!-- Assets Grid -->
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 max-h-[380px] overflow-y-auto p-1">
                        <template x-for="item in libraryAssets" :key="item.id">
                            <div
                                @click="selectAsset(item)"
                                class="group relative rounded-lg border overflow-hidden cursor-pointer bg-[#f6f6f9] transition"
                                :class="selectedItem && selectedItem.id === item.id ? 'border-[#4945ff] ring-2 ring-[#4945ff]/20' : 'border-[#eaeaef] hover:border-[#4945ff]'"
                            >
                                <div class="aspect-square bg-[#181826] relative overflow-hidden flex items-center justify-center">
                                    <img loading="lazy" decoding="async" :src="item.url" :alt="item.name" class="w-full h-full object-cover">
                                    <template x-if="selectedItem && selectedItem.id === item.id">
                                        <div class="absolute inset-0 bg-[#4945ff]/30 flex items-center justify-center">
                                            <div class="w-6 h-6 rounded-full bg-[#4945ff] text-white flex items-center justify-center text-[10px] shadow">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class="p-1.5 bg-white border-t border-[#eaeaef]">
                                    <p class="text-[11px] font-medium text-[#32324d] truncate" x-text="item.name"></p>
                                    <p class="text-[9px] text-[#8e8ea9]" x-text="item.formatted_size"></p>
                                </div>
                            </div>
                        </template>

                        <template x-if="libraryAssets.length === 0 && !loading">
                            <div class="col-span-full py-12 text-center text-xs text-[#8e8ea9]">
                                No assets found. You can upload one in the "Upload from computer" tab.
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Tab 2: Upload from Computer -->
                <div x-show="activeTab === 'upload'" class="space-y-4">
                    <div
                        class="border-2 border-dashed border-[#dcdce4] hover:border-[#4945ff] rounded-xl p-10 text-center bg-[#f6f6f9]/60 transition cursor-pointer"
                        @dragover.prevent
                        @drop.prevent="uploadDroppedFile($event)"
                        @click="$refs.computerInput.click()"
                    >
                        <input
                            type="file"
                            accept="image/*"
                            class="hidden"
                            x-ref="computerInput"
                            @change="uploadFile($event.target.files[0])"
                        >
                        <div class="w-12 h-12 rounded-full bg-[#f0ebfc] text-[#4945ff] flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-cloud-arrow-up text-lg"></i>
                        </div>
                        <p class="text-xs font-medium text-[#32324d]">
                            Drag &amp; drop your image here or <span class="text-[#4945ff] underline">browse files</span>
                        </p>
                        <p class="text-[11px] text-[#8e8ea9] mt-1">PNG, JPG, WEBP, GIF up to 20MB</p>
                    </div>

                    <template x-if="uploading">
                        <div class="p-3 bg-purple-50 text-[#4945ff] rounded-lg text-xs flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin"></i>
                            <span>Uploading asset to Strapi Media Library...</span>
                        </div>
                    </template>
                </div>

                <!-- Tab 3: From URL -->
                <div x-show="activeTab === 'url'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-[#32324d] mb-1.5">Image Web URL</label>
                        <input
                            type="url"
                            x-model="urlInput"
                            placeholder="https://images.unsplash.com/photo-..."
                            class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff]"
                        >
                    </div>
                    <button
                        type="button"
                        @click="selectFromUrl()"
                        :disabled="!urlInput"
                        class="px-4 py-2 bg-[#4945ff] hover:bg-[#3b37eb] disabled:opacity-50 text-white text-xs font-medium rounded-lg transition shadow-sm"
                    >
                        Insert from URL
                    </button>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 px-6 border-t border-[#eaeaef] bg-[#f6f6f9] flex items-center justify-between shrink-0">
                <span class="text-xs text-[#8e8ea9]" x-text="selectedItem ? '1 asset selected' : 'No asset selected'"></span>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="openModal = false"
                        class="px-3.5 py-2 bg-white hover:bg-[#eaeaef] text-[#666687] text-xs font-medium rounded-lg transition border border-[#dcdce4]"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="confirmSelection()"
                        :disabled="!selectedItem"
                        class="px-4 py-2 bg-[#4945ff] hover:bg-[#3b37eb] disabled:opacity-50 text-white text-xs font-medium rounded-lg transition shadow-sm"
                    >
                        Finish &amp; select
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function strapiAssetPicker(config) {
    return {
        fieldName: config.fieldName,
        currentValue: config.initialValue,
        currentUrl: config.initialUrl,
        currentName: '',
        openModal: false,
        activeTab: 'library',
        libraryAssets: [],
        selectedItem: null,
        searchQuery: '',
        urlInput: '',
        loading: false,
        uploading: false,

        loadLibrary() {
            this.loading = true;
            fetch('{{ route('strapi.media.index') }}?q=' + encodeURIComponent(this.searchQuery), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.libraryAssets = data.media || [];
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
            });
        },

        selectAsset(item) {
            this.selectedItem = item;
        },

        confirmSelection() {
            if (this.selectedItem) {
                this.currentValue = this.selectedItem.file_path;
                this.currentUrl = this.selectedItem.url;
                this.currentName = this.selectedItem.name;
            }
            this.openModal = false;
        },

        selectFromUrl() {
            if (this.urlInput) {
                this.currentValue = this.urlInput;
                this.currentUrl = this.urlInput;
                this.currentName = 'Remote Web Image';
                this.openModal = false;
            }
        },

        uploadFile(file) {
            if (!file) return;
            this.uploading = true;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route('strapi.media.store') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                this.uploading = false;
                if (data.media) {
                    this.currentValue = data.media.file_path;
                    this.currentUrl = data.media.url;
                    this.currentName = data.media.name;
                    this.openModal = false;
                }
            })
            .catch(err => {
                this.uploading = false;
                alert('Upload failed. Please try again.');
            });
        },

        uploadDroppedFile(event) {
            const dt = event.dataTransfer;
            if (dt && dt.files && dt.files[0]) {
                this.uploadFile(dt.files[0]);
            }
        },

        handleDirectFile(file) {
            if (file) {
                this.uploadFile(file);
            }
        },

        clearAsset() {
            this.currentValue = '';
            this.currentUrl = '';
            this.currentName = '';
            this.selectedItem = null;
        }
    };
}
</script>
