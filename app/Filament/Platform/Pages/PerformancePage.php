<?php

declare(strict_types=1);

namespace App\Filament\Platform\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PerformancePage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Performance';

    protected static ?string $title = 'Drutogoti Performance';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected string $view = 'filament.platform.pages.performance';

    /**
     * @return array<string, mixed>
     */
    public function getStatusProperty(): array
    {
        $enabled = (bool) config('drutogoti.enabled', false);
        $suggestions = 0;
        $requests = 0;

        if (Schema::hasTable('drutogoti_suggestions')) {
            $suggestions = (int) DB::table('drutogoti_suggestions')->count();
        }
        if (Schema::hasTable('drutogoti_requests')) {
            $requests = (int) DB::table('drutogoti_requests')->count();
        }

        return [
            'enabled' => $enabled,
            'storage' => config('drutogoti.storage.driver', 'sync'),
            'sample_rate' => config('drutogoti.sample_rate', 1),
            'suggestions' => $suggestions,
            'requests' => $requests,
            'package' => 'imrandevbd/drutogoti',
        ];
    }
}
