<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\StockAdjustments\Pages;

use App\Enums\Inventory\StockAdjustmentStatus;
use App\Filament\App\Resources\StockAdjustments\StockAdjustmentResource;
use App\Models\StockAdjustment;
use App\Services\Inventory\StockAdjustmentService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStockAdjustment extends EditRecord
{
    protected static string $resource = StockAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('post')
                ->label('Post adjustment')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->stockAdjustment()->status === StockAdjustmentStatus::Draft)
                ->action(function (StockAdjustmentService $service): void {
                    $service->post($this->stockAdjustment());
                    Notification::make()->title('Adjustment posted')->success()->send();
                    $this->refreshFormData(['status', 'posted_at']);
                }),
            DeleteAction::make()
                ->visible(fn (): bool => $this->stockAdjustment()->status === StockAdjustmentStatus::Draft),
        ];
    }

    private function stockAdjustment(): StockAdjustment
    {
        $record = $this->getRecord();
        if (! $record instanceof StockAdjustment) {
            throw new \LogicException('Expected a StockAdjustment record.');
        }

        return $record;
    }
}
