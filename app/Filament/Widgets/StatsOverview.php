<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Contribution;
use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $publishedCount = Article::where('status', 'published')->count();
        $totalArticles = Article::count();
        $totalViews = (int) Article::sum('views');
        $upcomingEvents = Event::upcoming()->count();
        $pendingSubmissions = Contribution::where('status', 'pending')->count();

        return [
            Stat::make('Total Articles', number_format($totalArticles))
                ->description("{$publishedCount} published • ".($totalArticles - $publishedCount).' drafts')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('primary'),

            Stat::make('Total Page Views', number_format($totalViews))
                ->description('Akumulasi pembaca portal')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make('Upcoming Events', $upcomingEvents)
                ->description('Agenda kalender & festival')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Pending Submissions', $pendingSubmissions)
                ->description($pendingSubmissions > 0 ? 'Menunggu kurasi redaksi' : 'Semua telah ditinjau')
                ->descriptionIcon('heroicon-m-inbox-stack')
                ->color($pendingSubmissions > 0 ? 'warning' : 'gray'),
        ];
    }
}
