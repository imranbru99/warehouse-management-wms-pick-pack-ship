<?php

declare(strict_types=1);

namespace App\Services\Outbound;

/**
 * Simple 3D first-fit decreasing bin packing heuristic.
 */
class CartonizationService
{
    /**
     * @param  array<int, array{id: int|string, length: float, width: float, height: float, qty?: int}>  $items
     * @param  array{length: float, width: float, height: float}  $cartonSpec
     * @return array<int, array<int, array{id: int|string, length: float, width: float, height: float, qty: int}>>
     */
    public function pack(array $items, array $cartonSpec): array
    {
        $expanded = [];
        foreach ($items as $item) {
            $qty = max(1, (int) ($item['qty'] ?? 1));
            for ($i = 0; $i < $qty; $i++) {
                $expanded[] = [
                    'id' => $item['id'],
                    'length' => (float) $item['length'],
                    'width' => (float) $item['width'],
                    'height' => (float) $item['height'],
                    'qty' => 1,
                ];
            }
        }

        usort($expanded, fn ($a, $b) => $this->volume($b) <=> $this->volume($a));

        $cartons = [];
        $current = [];
        $remainingVolume = $this->volume($cartonSpec);

        foreach ($expanded as $piece) {
            $pieceVolume = $this->volume($piece);

            if (! $this->fitsDimensions($piece, $cartonSpec)) {
                continue;
            }

            if ($pieceVolume > $remainingVolume && count($current) > 0) {
                $cartons[] = $current;
                $current = [];
                $remainingVolume = $this->volume($cartonSpec);
            }

            if ($pieceVolume <= $remainingVolume) {
                $current[] = $piece;
                $remainingVolume -= $pieceVolume;
            } else {
                $cartons[] = [$piece];
                $remainingVolume = $this->volume($cartonSpec);
            }
        }

        if (count($current) > 0) {
            $cartons[] = $current;
        }

        return $cartons;
    }

    /**
     * @param  array{length: float, width: float, height: float}  $item
     */
    private function volume(array $item): float
    {
        return (float) $item['length'] * (float) $item['width'] * (float) $item['height'];
    }

    /**
     * @param  array{length: float, width: float, height: float}  $item
     * @param  array{length: float, width: float, height: float}  $carton
     */
    private function fitsDimensions(array $item, array $carton): bool
    {
        $dims = [
            [(float) $item['length'], (float) $item['width'], (float) $item['height']],
        ];

        foreach ($dims as [$l, $w, $h]) {
            if ($l <= (float) $carton['length'] && $w <= (float) $carton['width'] && $h <= (float) $carton['height']) {
                return true;
            }
            if ($l <= (float) $carton['width'] && $w <= (float) $carton['length'] && $h <= (float) $carton['height']) {
                return true;
            }
        }

        return false;
    }
}
