<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentPackage extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'shipment_id',
        'carton_id',
        'tracking_number',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:3',
        ];
    }

    /** @return BelongsTo<Shipment, $this> */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /** @return BelongsTo<Carton, $this> */
    public function carton(): BelongsTo
    {
        return $this->belongsTo(Carton::class);
    }
}
