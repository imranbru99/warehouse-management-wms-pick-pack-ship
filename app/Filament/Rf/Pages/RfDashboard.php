<?php

declare(strict_types=1);

namespace App\Filament\Rf\Pages;

use App\Models\PickTask;
use App\Models\PutawayTask;
use App\Models\PurchaseOrder;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class RfDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDevicePhoneMobile;

    protected static ?string $navigationLabel = 'RF Home';

    protected static ?string $title = 'RF Scanner';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.rf.pages.dashboard';

    public int $openPutaways = 0;

    public int $openPicks = 0;

    public int $openPos = 0;

    public function mount(): void
    {
        $this->openPutaways = PutawayTask::query()->whereNotIn('status', ['completed', 'cancelled'])->count();
        $this->openPicks = PickTask::query()->whereNotIn('status', ['completed', 'cancelled'])->count();
        $this->openPos = PurchaseOrder::query()->whereIn('status', ['submitted', 'partial'])->count();
    }
}
