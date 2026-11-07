<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Contracts\View\View;

class TrackShipmentController extends Controller
{
    public function show(string $number): View
    {
        $shipment = Shipment::query()
            ->withoutGlobalScopes()
            ->with(['trackingEvents', 'salesOrder'])
            ->where(function ($q) use ($number) {
                $q->where('tracking_number', $number)
                    ->orWhere('number', $number);
            })
            ->firstOrFail();

        return view('track.show', [
            'shipment' => $shipment,
        ]);
    }
}
