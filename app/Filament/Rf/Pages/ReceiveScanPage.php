<?php

declare(strict_types=1);

namespace App\Filament\Rf\Pages;

use App\Enums\Inbound\PurchaseOrderStatus;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Services\Inbound\ReceivingService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class ReceiveScanPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static ?string $navigationLabel = 'Receive';

    protected static ?string $title = 'RF Receive';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.rf.pages.receive-scan';

    public string $po_number = '';

    public ?int $location_id = null;

    /** @return Collection<int, Location> */
    public function getLocationsProperty(): Collection
    {
        return Location::query()
            ->whereIn('type', ['receiving', 'staging', 'dock', 'storage'])
            ->orderBy('code')
            ->get();
    }

    public function submit(ReceivingService $receiving): void
    {
        $po = PurchaseOrder::query()
            ->where('number', trim($this->po_number))
            ->whereIn('status', [PurchaseOrderStatus::Submitted, PurchaseOrderStatus::Partial])
            ->with('lines')
            ->first();

        if ($po === null || $this->location_id === null) {
            Notification::make()->title('PO or dock location missing')->danger()->send();

            return;
        }

        $location = Location::query()->findOrFail($this->location_id);
        $lines = $po->lines
            ->map(function (PurchaseOrderLine $line): ?array {
                $remaining = bcsub((string) $line->qty_ordered, (string) $line->qty_received, 3);
                if (bccomp($remaining, '0', 3) <= 0) {
                    return null;
                }

                return [
                    'purchase_order_line_id' => $line->id,
                    'qty' => $remaining,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if ($lines === []) {
            Notification::make()->title('Nothing left to receive')->warning()->send();

            return;
        }

        $receipt = $receiving->receive($po, $lines, $location, auth()->user());
        Notification::make()->title('Received '.$receipt->number)->success()->send();
        $this->reset('po_number');
    }
}
