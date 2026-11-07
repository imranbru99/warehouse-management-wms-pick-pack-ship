<?php

declare(strict_types=1);

use App\Models\Location;
use App\Services\Outbound\PickPathOptimizer;

it('orders locations in serpentine aisle and level sequence', function () {
    $optimizer = new PickPathOptimizer;

    $locations = collect([
        Location::make(['aisle' => '02', 'level' => '2', 'pick_sequence' => 1, 'code' => 'B2']),
        Location::make(['aisle' => '01', 'level' => '1', 'pick_sequence' => 1, 'code' => 'A1']),
        Location::make(['aisle' => '01', 'level' => '2', 'pick_sequence' => 2, 'code' => 'A2']),
        Location::make(['aisle' => '02', 'level' => '1', 'pick_sequence' => 2, 'code' => 'B1']),
    ]);

    $ordered = $optimizer->optimizeLocations($locations)->pluck('code')->all();

    expect($ordered)->toBe(['A2', 'A1', 'B1', 'B2']);
});
