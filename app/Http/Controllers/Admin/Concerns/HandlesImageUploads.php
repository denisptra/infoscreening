<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\Expert;
use App\Models\Media;
use App\Models\Product;
use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait HandlesImageUploads
{
    /**
     * Validation rules for fields wired to the Strapi Media Picker (accepts either uploaded file or existing/selected path).
     *
     * @return array<string, array<int, mixed>>
     */
    protected function imagePickerRules(Request $request, string $field): array
    {
        return [
            $field => $request->hasFile($field)
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:20480']
                : ['nullable', 'string', 'max:500'],
            "direct_{$field}" => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:20480'],
            "{$field}_url" => ['nullable', 'string', 'max:500'],
            "remove_{$field}" => ['nullable', 'boolean'],
        ];
    }

    /**
     * Resolve the image value for a record from an uploaded file, Strapi asset picker, or a remote URL.
     * Returns the existing value when neither is supplied.
     */
    protected function resolveImage(Request $request, string $field, ?string $current = null, string $directory = 'uploads'): ?string
    {
        if ($request->boolean("remove_{$field}")) {
            $this->deleteStoredImage($current);

            return null;
        }

        // Direct file upload or fallback direct file
        $file = $request->file($field) ?: $request->file("direct_{$field}");
        if ($file) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $mime = $file->getMimeType();
            $extension = strtolower($file->getClientOriginalExtension());

            if (! in_array($mime, $allowedMimes, true) || ! in_array($extension, $allowedExtensions, true)) {
                throw ValidationException::withMessages([
                    $field => 'Berkas harus berupa gambar valid (JPEG, PNG, WEBP, GIF).',
                ]);
            }

            $path = $file->store($directory.'/'.date('Y/m'), 'public');
            if (! is_string($path) || $path === '') {
                throw ValidationException::withMessages([
                    $field => 'Gagal menyimpan gambar ke direktori penyimpanan publik.',
                ]);
            }

            if ($current !== $path) {
                $this->deleteStoredImage($current);
            }

            // Automatically record in Strapi Media Library
            Media::firstOrCreate(
                ['file_path' => $path],
                [
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize() ?: 0,
                    'caption' => $request->input('title') ?? $request->input('name'),
                ]
            );

            return $path;
        }

        // Selected from Strapi Media Library or typed URL
        $selectedPath = $request->input($field);
        if (filled($selectedPath)) {
            if (Str::contains($selectedPath, ['..', '\\'])) {
                throw ValidationException::withMessages([
                    $field => 'Format path gambar tidak valid.',
                ]);
            }

            return MediaUrl::normalizeForStorage((string) $selectedPath);
        }

        $url = $request->input("{$field}_url");
        if (filled($url)) {
            if (Str::contains((string) $url, ['..', '\\'])) {
                throw ValidationException::withMessages([
                    $field => 'Format URL gambar tidak valid.',
                ]);
            }

            $normalizedUrl = MediaUrl::normalizeForStorage((string) $url);
            if ($current !== $normalizedUrl) {
                $this->deleteStoredImage($current);
            }

            return $normalizedUrl;
        }

        return $current;
    }

    protected function deleteStoredImage(?string $path): void
    {
        if (
            blank($path)
            || Str::startsWith($path, ['http://', 'https://', '//', '/', '~'])
            || Str::contains($path, ['..', '\\'])
        ) {
            return;
        }

        if (Media::query()->where('file_path', $path)->exists()) {
            return;
        }

        $references = Article::query()->where('featured_image', $path)->count()
            + Event::query()->where('image', $path)->count()
            + Expert::query()->where('photo', $path)->count()
            + Product::query()->where('image', $path)->count()
            + Category::query()->where('hero_image', $path)->count();

        if ($references > 1) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
