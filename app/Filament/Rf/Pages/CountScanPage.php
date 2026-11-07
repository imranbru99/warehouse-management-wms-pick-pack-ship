<?php

declare(strict_types=1);

namespace App\Filament\Rf\Pages;

use App\Enums\Inventory\StockAdjustmentStatus;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentLine;
use App\Models\StockBalance;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class CountScanPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $navigationLabel = 'Count';

    protected static ?string $title = 'RF Count';

    protected static ?int $navigationSort = 50;

    protected string $view = 'filament.rf.pages.count-scan';

    public string $location_code = '';

    public string $sku = '';

    public string $counted_qty = '';

    public function submit(): void
    {
        $location = Location::query()
            ->where('code', trim($this->location_code))
            ->orWhere('barcode', trim($this->location_code))
            ->first();
        $product = Product::query()->where('sku', trim($this->sku))->first();

        if ($location === null || $product === null || $this->counted_qty === '') {
            Notification::make()->title('Location, SKU, and qty are required')->danger()->send();

            return;
        }

        $systemQty = (string) (StockBalance::query()
            ->where('location_id', $location->id)
            ->where('product_id', $product->id)
            ->sum('qty'));

        $adjustment = StockAdjustment::query()->create([
            'warehouse_id' => $location->warehouse_id,
            'number' => 'ADJ-RF-'.Str::upper(Str::random(6)),
            'status' => StockAdjustmentStatus::Draft,
            'reason' => 'cycle_count',
            'notes' => 'RF counted '.$this->counted_qty.' vs system '.$systemQty,
            'created_by' => auth()->id(),
        ]);

        StockAdjustmentLine::query()->create([
            'stock_adjustment_id' => $adjustment->id,
            'product_id' => $product->id,
            'location_id' => $location->id,
            'qty_delta' => bcsub($this->counted_qty, $systemQty, 3),
            'line_number' => 1,
        ]);

        Notification::make()->title('Draft adjustment '.$adjustment->number.' created')->success()->send();
        $this->reset('sku', 'counted_qty');
    }
}
