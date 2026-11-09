@extends('layouts.admin')

@section('page_title', $article->exists ? 'Edit Article: ' . $article->title : 'Create an Entry: Article')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Strapi Form Top Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('strapi.articles.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline flex items-center gap-1.5 mb-1">
                <i class="fas fa-arrow-left text-[10px]"></i>
                <span>Back to Articles list</span>
            </a>
            <h2 class="font-sans font-semibold text-xl text-[#32324d]">
                {{ $article->exists ? 'Edit entry' : 'Create an entry' }}
            </h2>
        </div>

        @if ($article->exists)
            <a href="{{ route('articles.show', ['article' => $article]) }}" target="_blank" class="text-xs font-medium text-[#666687] hover:text-[#4945ff] flex items-center gap-1.5">
                <span>View on Live Site</span>
                <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
            </a>
        @endif
    </div>

    <form
        method="POST"
        action="{{ $article->exists ? route('strapi.articles.update', $article) : route('strapi.articles.store') }}"
        enctype="multipart/form-data"
        class="space-y-6"
    >
        @csrf
        @if ($article->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Main Form Card (8 cols) -->
            <div class="lg:col-span-8 bg-white p-6 rounded-xl border border-[#eaeaef] shadow-strapi space-y-5">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-medium text-[#32324d] mb-1.5">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        value="{{ old('title', $article->title) }}"
                        required
                        class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff] text-[#32324d]"
                        placeholder="Article title..."
                    >
                    @error('title')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-xs font-medium text-[#32324d] mb-1.5">
                        Slug (URL Key)
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        value="{{ old('slug', $article->slug) }}"
                        class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs font-mono text-[#666687] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                        placeholder="article-slug"
                    >
                    @error('slug')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Excerpt -->
                <div>
                    <label for="excerpt" class="block text-xs font-medium text-[#32324d] mb-1.5">
                        Excerpt / Summary
                    </label>
                    <textarea
                        name="excerpt"
                        id="excerpt"
                        rows="2"
                        class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                        placeholder="Short summary of the article..."
                    >{{ old('excerpt', $article->excerpt) }}</textarea>
                    @error('excerpt')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content WYSIWYG Editor (Word Style) -->
                <div>
                    <label for="content" class="block text-xs font-medium text-[#32324d] mb-1.5 flex items-center justify-between">
                        <span>Content Body <span class="text-red-500">*</span></span>
                        <span class="text-[11px] text-[#8e8ea9] font-normal">Rich Text Editor</span>
                    </label>

                    <div class="border border-[#dcdce4] rounded-lg overflow-hidden focus-within:border-[#4945ff] focus-within:ring-1 focus-within:ring-[#4945ff] bg-white transition shadow-xs">
                        <!-- Toolbar -->
                        <div class="flex items-center flex-wrap gap-1 p-2 bg-[#f6f6f9] border-b border-[#eaeaef] text-[#4a4a6a]">
                            <!-- Headings & Format -->
                            <button type="button" onclick="formatDoc('formatBlock', '<h2>')" title="Heading 2 (Judul Bagian)" class="p-1.5 px-2 rounded hover:bg-white hover:shadow-xs transition text-xs font-bold text-[#32324d]">
                                H2
                            </button>
                            <button type="button" onclick="formatDoc('formatBlock', '<h3>')" title="Heading 3 (Subjudul)" class="p-1.5 px-2 rounded hover:bg-white hover:shadow-xs transition text-xs font-semibold text-[#32324d]">
                                H3
                            </button>
                            <button type="button" onclick="formatDoc('formatBlock', '<p>')" title="Paragraf Biasa" class="p-1.5 px-2 rounded hover:bg-white hover:shadow-xs transition text-xs font-normal text-[#32324d]">
                                P
                            </button>

                            <div class="w-px h-5 bg-[#dcdce4] mx-1"></div>

                            <!-- Text Formatting -->
                            <button type="button" onclick="formatDoc('bold')" title="Tebal (Ctrl+B)" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs font-bold">
                                <i class="fas fa-bold"></i>
                            </button>
                            <button type="button" onclick="formatDoc('italic')" title="Miring (Ctrl+I)" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs italic">
                                <i class="fas fa-italic"></i>
                            </button>
                            <button type="button" onclick="formatDoc('underline')" title="Garis Bawah (Ctrl+U)" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs underline">
                                <i class="fas fa-underline"></i>
                            </button>
                            <button type="button" onclick="formatDoc('strikeThrough')" title="Coret Teks" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-strikethrough"></i>
                            </button>

                            <div class="w-px h-5 bg-[#dcdce4] mx-1"></div>

                            <!-- Alignment -->
                            <button type="button" onclick="formatDoc('justifyLeft')" title="Rata Kiri" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-align-left"></i>
                            </button>
                            <button type="button" onclick="formatDoc('justifyCenter')" title="Rata Tengah" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-align-center"></i>
                            </button>
                            <button type="button" onclick="formatDoc('justifyRight')" title="Rata Kanan" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-align-right"></i>
                            </button>
                            <button type="button" onclick="formatDoc('justifyFull')" title="Rata Kiri Kanan" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-align-justify"></i>
                            </button>

                            <div class="w-px h-5 bg-[#dcdce4] mx-1"></div>

                            <!-- Lists & Quotes -->
                            <button type="button" onclick="formatDoc('insertUnorderedList')" title="Bullet List" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-list-ul"></i>
                            </button>
                            <button type="button" onclick="formatDoc('insertOrderedList')" title="Numbered List" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-list-ol"></i>
                            </button>
                            <button type="button" onclick="formatDoc('formatBlock', '<blockquote>')" title="Kutipan (Quote)" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-quote-left"></i>
                            </button>

                            <div class="w-px h-5 bg-[#dcdce4] mx-1"></div>

                            <!-- Link & Horizontal Line -->
                            <button type="button" onclick="insertLink()" title="Sisipkan Link Tautan" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-link"></i>
                            </button>
                            <button type="button" onclick="formatDoc('unlink')" title="Hapus Link" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-unlink"></i>
                            </button>
                            <button type="button" onclick="formatDoc('insertHorizontalRule')" title="Garis Pemisah" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" onclick="formatDoc('removeFormat')" title="Hapus Format" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs text-red-500">
                                <i class="fas fa-eraser"></i>
                            </button>

                            <div class="w-px h-5 bg-[#dcdce4] mx-1"></div>

                            <!-- Undo / Redo -->
                            <button type="button" onclick="formatDoc('undo')" title="Undo (Ctrl+Z)" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button type="button" onclick="formatDoc('redo')" title="Redo (Ctrl+Y)" class="w-7 h-7 flex items-center justify-center rounded hover:bg-white hover:shadow-xs transition text-xs">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>

                        <!-- Editable Visual Canvas -->
                        <div
                            id="editorCanvas"
                            contenteditable="true"
                            class="p-4 min-h-[350px] max-h-[600px] overflow-y-auto text-sm leading-relaxed text-[#32324d] outline-none focus:outline-none prose prose-sm max-w-none"
                            placeholder="Mulai tulis naskah artikel Anda di sini seperti di Microsoft Word..."
                        >{!! old('content', $article->content) !!}</div>
                    </div>

                    <!-- Hidden textarea mapped for Laravel Form POST -->
                    <textarea
                        name="content"
                        id="content"
                        required
                        class="hidden"
                    >{{ old('content', $article->content) }}</textarea>

                    @error('content')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Side Settings Column (4 cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Publishing Card -->
                <div class="bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi space-y-4 text-xs">
                    <h3 class="font-medium text-xs text-[#32324d] pb-2 border-b border-[#eaeaef]">
                        Entry Settings
                    </h3>

                    @if (auth()->user()->isEditor())
                        <div class="rounded-lg bg-blue-50 p-3 text-[11px] leading-relaxed text-blue-800">
                            Artikel disimpan sebagai draf dan perlu diterbitkan oleh Admin atau Super Admin.
                        </div>
                    @else
                    <div>
                        <label for="status" class="block font-medium text-[#32324d] mb-1">
                            Status
                        </label>
                        <select
                            name="status"
                            id="status"
                            class="w-full border border-[#dcdce4] rounded-md px-3 py-1.5 text-xs text-[#32324d] focus:border-[#4945ff]"
                        >
                            <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $article->status) === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                    @endif

                    @if (! auth()->user()->isEditor())
                    <div>
                        <label for="published_at" class="block font-medium text-[#32324d] mb-1">
                            Publish Date
                        </label>
                        <input
                            type="datetime-local"
                            name="published_at"
                            id="published_at"
                            value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i')) }}"
                            class="w-full border border-[#dcdce4] rounded-md px-3 py-1.5 text-xs text-[#32324d]"
                        >
                    </div>
                    @endif

                    @if (! auth()->user()->isEditor())
                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                name="is_featured"
                                value="1"
                                {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}
                                class="rounded border-[#dcdce4] text-[#4945ff] focus:ring-[#4945ff]"
                            >
                            <span class="text-xs text-[#32324d] font-medium">Hero Featured Article</span>
                        </label>
                    </div>
                    @endif

                    <div class="pt-3 border-t border-[#eaeaef]">
                        <button
                            type="submit"
                            class="w-full py-2 bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium rounded-lg transition shadow-sm flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-check text-[10px]"></i>
                            <span>{{ auth()->user()->isEditor() ? 'Simpan Draf' : 'Simpan Artikel' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Strapi Media Upload Card -->
                <div class="bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi space-y-3.5 text-xs">
                    <x-strapi-media-picker
                        name="featured_image"
                        label="Featured Image Asset"
                        :value="old('featured_image', $article->featured_image)"
                        :imageUrl="$article->image_url"
                    />
                </div>

                <!-- Relations Card: Categories & Tags -->
                <div class="bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi space-y-4 text-xs">
                    <h3 class="font-medium text-xs text-[#32324d] pb-2 border-b border-[#eaeaef]">
                        Categories Relation
                    </h3>

                    <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                        @php
                            $selectedCats = old('categories', $article->categories->pluck('id')->toArray());
                        @endphp
                        @foreach ($categories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer text-slate-700">
                                <input
                                    type="checkbox"
                                    name="categories[]"
                                    value="{{ $cat->id }}"
                                    {{ in_array($cat->id, $selectedCats) ? 'checked' : '' }}
                                    class="rounded border-[#dcdce4] text-[#4945ff] focus:ring-[#4945ff]"
                                >
                                <span>{{ $cat->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

<script>
function formatDoc(cmd, value = null) {
    document.execCommand(cmd, false, value);
    syncContent();
}

function insertLink() {
    const url = prompt('Masukkan URL tautan (contoh: https://google.com):');
    if (url) {
        document.execCommand('createLink', false, url);
        syncContent();
    }
}

function syncContent() {
    const canvas = document.getElementById('editorCanvas');
    const textarea = document.getElementById('content');
    if (canvas && textarea) {
        textarea.value = canvas.innerHTML;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('editorCanvas');
    const textarea = document.getElementById('content');
    const form = document.querySelector('form');

    if (canvas && textarea) {
        canvas.addEventListener('input', syncContent);
        canvas.addEventListener('blur', syncContent);
    }

    if (form) {
        form.addEventListener('submit', function() {
            syncContent();
        });
    }
});
</script>
@endsection
