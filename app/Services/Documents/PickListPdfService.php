<?php

declare(strict_types=1);

namespace App\Services\Documents;

use App\Models\SalesOrder;
use ImranDev\UnicodePdf\Facades\UnicodePdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PickListPdfService
{
    public function download(SalesOrder $order, string $locale = 'en'): StreamedResponse
    {
        $order->loadMissing(['lines.product', 'warehouse', 'pickTasks.location']);

        $preset = match ($locale) {
            'bn' => 'bengali',
            'ar' => 'arabic',
            'zh', 'zh-CN', 'zh-TW' => 'chinese',
            default => 'universal',
        };

        return UnicodePdf::preset($preset)
            ->locale($locale)
            ->loadView('pdf.pick-list', [
                'order' => $order,
                'locale' => $locale,
                'generatedAt' => now(),
            ])
            ->a4()
            ->download('pick-list-'.$order->number.'.pdf');
    }

    public function output(SalesOrder $order, string $locale = 'en'): string
    {
        $order->loadMissing(['lines.product', 'warehouse', 'pickTasks.location']);

        $preset = match ($locale) {
            'bn' => 'bengali',
            'ar' => 'arabic',
            'zh', 'zh-CN', 'zh-TW' => 'chinese',
            default => 'universal',
        };

        return UnicodePdf::preset($preset)
            ->locale($locale)
            ->loadView('pdf.pick-list', [
                'order' => $order,
                'locale' => $locale,
                'generatedAt' => now(),
            ])
            ->a4()
            ->output();
    }
}
