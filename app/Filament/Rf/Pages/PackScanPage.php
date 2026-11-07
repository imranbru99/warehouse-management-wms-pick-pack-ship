<?php

declare(strict_types=1);

namespace App\Filament\Rf\Pages;

use App\Enums\SalesOrderStatus;
use App\Models\SalesOrder;
use App\Services\Outbound\PackService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class PackScanPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Pack';

    protected static ?string $title = 'RF Pack';

    protected static ?int $navigationSort = 40;

    protected string $view = 'filament.rf.pages.pack-scan';

    public string $order_number = '';

    public function submit(PackService $pack): void
    {
        $order = SalesOrder::query()
            ->where('number', trim($this->order_number))
            ->whereIn('status', [
                SalesOrderStatus::Allocated,
                SalesOrderStatus::Picking,
                SalesOrderStatus::Picked,
            ])
            ->with('lines.product')
            ->first();

        if ($order === null) {
            Notification::make()->title('Order not ready to pack')->danger()->send();

            return;
        }

        $shipment = $pack->packOrder($order, [
            'length' => 50,
            'width' => 50,
            'height' => 50,
        ]);

        Notification::make()->title('Packed '.$shipment->number)->success()->send();
        $this->reset('order_number');
    }
}
