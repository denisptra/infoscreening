<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaController extends Controller
{
    /**
     * Display the Strapi Media Library view or return JSON for asset pickers.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Media::query()->latest();

        if ($q = $request->input('q')) {
            $query->where(function ($b) use ($q) {
                $b->where('name', 'like', "%{$q}%")
                    ->orWhere('caption', 'like', "%{$q}%")
                    ->orWhere('file_path', 'like', "%{$q}%");
            });
        }

        if ($type = $request->input('type')) {
            if ($type === 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($type === 'video') {
                $query->where('mime_type', 'like', 'video/%');
            } elseif ($type === 'document') {
                $query->where('mime_type', 'not like', 'image/%')
                    ->where('mime_type', 'not like', 'video/%');
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            $media = $query->take(60)->get();

            return response()->json([
                'success' => true,
                'media' => $media,
            ]);
        }

        $media = $query->paginate(24)->withQueryString();
        $totalCount = Media::count();
        $totalBytes = Media::sum('size');

        return view('admin.media.index', [
            'media' => $media,
            'totalCount' => $totalCount,
            'totalBytes' => $totalBytes,
        ]);
    }

    /**
     * Store new asset(s) via file upload or remote URL.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'files' => 'nullable|array|max:10',
            'files.*' => 'file|max:20480|mimes:jpg,jpeg,png,webp,gif,pdf,mp4,mov',
            'file' => 'nullable|file|max:20480|mimes:jpg,jpeg,png,webp,gif,pdf,mp4,mov',
            'url' => 'nullable|url|max:500',
            'caption' => 'nullable|string|max:255',
        ]);

        $created = [];

        // Single file upload
        if ($request->hasFile('file')) {
            $created[] = $this->handleFileUpload($request->file('file'), $request->input('caption'));
        }

        // Multiple files upload (drag & drop multiple)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $created[] = $this->handleFileUpload($file, $request->input('caption'));
            }
        }

        // Remote URL
        if ($url = $request->input('url')) {
            $filename = basename(parse_url($url, PHP_URL_PATH) ?? 'asset.jpg') ?: 'asset.jpg';
            $media = Media::create([
                'name' => $filename,
                'file_path' => $url,
                'mime_type' => 'image/jpeg',
                'size' => 0,
                'caption' => $request->input('caption'),
            ]);
            $created[] = $media;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'created' => $created,
                'media' => $created[0] ?? null,
            ]);
        }

        return redirect()->route('strapi.media.index')->with('success', 'Asset(s) uploaded successfully to Media Library.');
    }

    /**
     * Delete an asset from disk and database.
     */
    public function destroy(Request $request, Media $media): RedirectResponse|JsonResponse
    {
        if ($media->file_path && ! Str::startsWith($media->file_path, ['http://', 'https://', '//'])) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Asset deleted successfully.',
            ]);
        }

        return redirect()->route('strapi.media.index')->with('success', 'Asset deleted from Media Library.');
    }

    private function handleFileUpload($file, ?string $caption): Media
    {
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize() ?: 0;

        $path = $file->store('uploads/'.date('Y/m'), 'public');

        $width = null;
        $height = null;
        if (Str::startsWith($mimeType, 'image/')) {
            try {
                $fullPath = Storage::disk('public')->path($path);
                if (file_exists($fullPath)) {
                    $dimensions = @getimagesize($fullPath);
                    if ($dimensions) {
                        $width = $dimensions[0] ?? null;
                        $height = $dimensions[1] ?? null;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore dimension read errors
            }
        }

        return Media::create([
            'name' => $originalName,
            'file_path' => $path,
            'mime_type' => $mimeType,
            'size' => $size,
            'width' => $width,
            'height' => $height,
            'caption' => $caption,
        ]);
    }
}
