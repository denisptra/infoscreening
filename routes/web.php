<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContentTypeBuilderController as AdminContentTypeBuilderController;
use App\Http\Controllers\Admin\ContributionController as AdminContributionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ExpertController as AdminExpertController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\NavigationItemController as AdminNavigationItemController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\SiteAppearanceController as AdminSiteAppearanceController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContributionSubmissionController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// Public front-facing portal routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Articles
Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

// Categories
Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/kategori/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/category/{category:slug}', function (\App\Models\Category $category) {
    return redirect()->route('categories.show', ['category' => $category], 301);
});
Route::redirect('/category', '/kategori', 301);
Route::redirect('/categories', '/kategori', 301);

// Tags
Route::get('/tag', [TagController::class, 'index'])->name('tags.index');
Route::get('/tag/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

// Search
Route::get('/cari', [SearchController::class, 'index'])
    ->middleware('throttle:30,1')
    ->name('search.index');

// Events / Kalender
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/kalender', [EventController::class, 'index'])->name('calendar.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

// Contribution
Route::get('/berkontribusi', [ContributionSubmissionController::class, 'create'])->name('contribute.create');
Route::post('/berkontribusi', [ContributionSubmissionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contribute.store');

// Directory & Specialized Pages
Route::get('/authors-list', [DirectoryController::class, 'authors'])->name('directory.authors');
Route::get('/rupa-jasa', [DirectoryController::class, 'services'])->name('directory.services');
Route::get('/rupa-jasa/tenaga-ahli', [DirectoryController::class, 'experts'])->name('directory.experts');
Route::get('/toko', [DirectoryController::class, 'shop'])->name('directory.shop');

// Static Pages
Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', 'tentang-kami|kontak|kebijakan-privasi')
    ->name('pages.show');

// User Dashboard & Admin Routing
Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->canAccessCms()) {
        return redirect()->route('strapi.dashboard');
    }

    if (auth()->check()) {
        return redirect()->route('home')
            ->with('status', 'Akun ini tidak memiliki akses ke panel admin.');
    }

    return redirect()->route('login');
})->name('dashboard');

// Strapi Headless CMS (Alternative view)
Route::prefix('strapi')
    ->name('strapi.')
    ->middleware(['auth', 'role:super_admin,admin,editor'])
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::resource('articles', AdminArticleController::class)->except(['show']);

        // Content Manager Collections & Tampilan Navigasi: bisa diakses oleh Super Admin & Admin (Redaksi)
        Route::middleware('role:super_admin,admin')->group(function () {
            Route::resource('categories', AdminCategoryController::class)->except(['show']);
            Route::resource('tags', AdminTagController::class)->except(['show']);
            Route::resource('events', AdminEventController::class)->except(['show']);
            Route::resource('experts', AdminExpertController::class)->except(['show']);
            Route::resource('products', AdminProductController::class)->except(['show']);
            Route::post('media', [AdminMediaController::class, 'store'])
                ->middleware('throttle:20,1')
                ->name('media.store');
            Route::resource('media', AdminMediaController::class)->except(['create', 'edit', 'update', 'store']);
            Route::resource('contributions', AdminContributionController::class)->only(['index', 'show', 'update', 'destroy']);

            // Navigasi & Tampilan Footer
            Route::post('navigation/normalize-urls', [AdminNavigationItemController::class, 'normalizeUrls'])
                ->name('navigation.normalize-urls');
            Route::resource('navigation', AdminNavigationItemController::class)->except(['show']);
            Route::put('appearance/footer', [AdminSiteAppearanceController::class, 'updateFooter'])
                ->name('appearance.footer.update');
            Route::post('settings/clear-cache', function () {
                \Illuminate\Support\Facades\Artisan::call('optimize:clear');
                return back()->with('status', 'Cache sistem berhasil dibersihkan.');
            })->name('settings.clear-cache');
        });

        // Pengaturan Sistem & Konfigurasi Lanjutan: Khusus Super Admin
        Route::middleware('role:super_admin')->group(function () {
            Route::get('content-types', [AdminContentTypeBuilderController::class, 'index'])->name('content-types.index');
            Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');
            Route::post('settings/tokens', [AdminSettingsController::class, 'createToken'])
                ->middleware('throttle:10,1')
                ->name('settings.tokens.create');
            Route::patch('settings/users/{user}/role', [AdminSettingsController::class, 'updateUserRole'])
                ->name('settings.users.role');
        });
    });

// Auth Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
