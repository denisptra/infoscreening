<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait HandlesImageUploads
{
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

            $this->deleteStoredImage($current);
            $path = $file->store($directory.'/'.date('Y/m'), 'public');

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

            return $selectedPath;
        }

        $url = $request->input("{$field}_url");
        if (filled($url)) {
            if ($current !== $url) {
                $this->deleteStoredImage($current);
            }

            return $url;
        }

        return $current;
    }

    protected function deleteStoredImage(?string $path): void
    {
        if (
            filled($path)
            && ! Str::startsWith($path, ['http://', 'https://', '//', '/', '~'])
            && ! Str::contains($path, ['..', '\\'])
        ) {
            Storage::disk('public')->delete($path);
        }
    }
}
