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

                <!-- Content Markdown / WYSIWYG -->
                <div>
                    <label for="content" class="block text-xs font-medium text-[#32324d] mb-1.5">
                        Content Body <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="content"
                        id="content"
                        rows="14"
                        required
                        class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2.5 text-xs font-normal leading-relaxed text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                        placeholder="Write article body content here..."
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

                    <div class="pt-3 border-t border-[#eaeaef]">
                        <button
                            type="submit"
                            class="w-full py-2 bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium rounded-lg transition shadow-sm flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-check text-[10px]"></i>
                            <span>{{ $article->exists ? 'Save Changes' : 'Publish Entry' }}</span>
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
@endsection
