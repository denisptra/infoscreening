<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\SiteSetting;
use App\Support\MediaLibrarySync;
use App\Support\MediaUrl;
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
    public function index(Request $request, MediaLibrarySync $sync): View|JsonResponse
    {
        if (! app()->runningUnitTests() && ! SiteSetting::query()->where('key', 'media_backup_synced_v1')->exists()) {
            $sync->sync();
            SiteSetting::query()->updateOrCreate(
                ['key' => 'media_backup_synced_v1'],
                ['value' => now()->toIso8601String()]
            );
        }

        $query = Media::query()->latest('created_at')->latest('id');

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
            $paginator = $query->paginate(48);

            return response()->json([
                'success' => true,
                'media' => $paginator->items(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
                'current_page' => $paginator->currentPage(),
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
     * Synchronize existing content images and public disk assets into the Media Library.
     */
    public function sync(MediaLibrarySync $sync): RedirectResponse
    {
        $stats = $sync->sync();
        SiteSetting::query()->updateOrCreate(
            ['key' => 'media_backup_synced_v1'],
            ['value' => now()->toIso8601String()]
        );

        return redirect()
            ->route('strapi.media.index')
            ->with('status', sprintf(
                'Sinkronisasi selesai: %s aset baru dimasukkan ke Media Library (%s dari konten/backup, %s dari folder storage).',
                number_format($stats['added']),
                number_format($stats['from_content']),
                number_format($stats['from_storage'])
            ));
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
            $normalizedUrl = MediaUrl::normalizeForStorage($url) ?? $url;
            $filename = basename(parse_url($normalizedUrl, PHP_URL_PATH) ?? 'asset.jpg') ?: 'asset.jpg';
            $media = Media::create([
                'name' => $filename,
                'file_path' => $normalizedUrl,
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

        return redirect()->route('strapi.media.index')->with('status', 'Asset(s) uploaded successfully to Media Library.');
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

        return redirect()->route('strapi.media.index')->with('status', 'Asset deleted from Media Library.');
    }

    private function handleFileUpload($file, ?string $caption): Media
    {
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize() ?: 0;

        $path = $file->store('uploads/'.date('Y/m'), 'public');
        if (! is_string($path) || $path === '') {
            throw new \RuntimeException('Gagal menyimpan aset ke disk penyimpanan publik.');
        }

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
