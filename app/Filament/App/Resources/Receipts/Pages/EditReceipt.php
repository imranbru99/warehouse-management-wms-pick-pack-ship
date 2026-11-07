<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Receipts\Pages;

use App\Filament\App\Resources\Receipts\ReceiptResource;
use Filament\Resources\Pages\EditRecord;

class EditReceipt extends EditRecord
{
    protected static string $resource = ReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
