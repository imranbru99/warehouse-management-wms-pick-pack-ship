<?php

declare(strict_types=1);

use App\Services\Outbound\CartonizationService;

it('packs items that fit within carton dimensions using first-fit decreasing', function () {
    $service = new CartonizationService;

    $carton = ['length' => 30.0, 'width' => 30.0, 'height' => 30.0];
    $items = [
        ['id' => 1, 'length' => 10.0, 'width' => 10.0, 'height' => 10.0, 'qty' => 2],
        ['id' => 2, 'length' => 20.0, 'width' => 15.0, 'height' => 10.0, 'qty' => 1],
    ];

    $packed = $service->pack($items, $carton);

    expect($packed)->not->toBeEmpty();

    $pieceCount = collect($packed)->flatten(1)->count();
    expect($pieceCount)->toBe(3);

    foreach ($packed as $cartonItems) {
        foreach ($cartonItems as $piece) {
            expect($piece['length'])->toBeLessThanOrEqual(30.0);
            expect($piece['width'])->toBeLessThanOrEqual(30.0);
            expect($piece['height'])->toBeLessThanOrEqual(30.0);
        }
    }
});
