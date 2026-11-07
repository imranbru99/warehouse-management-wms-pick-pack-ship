<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Carton extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'shipment_id',
        'code',
        'length',
        'width',
        'height',
        'weight',
        'status',
        'contents',
    ];

    protected function casts(): array
    {
        return [
            'length' => 'decimal:3',
            'width' => 'decimal:3',
            'height' => 'decimal:3',
            'weight' => 'decimal:3',
            'contents' => 'array',
        ];
    }

    /** @return BelongsTo<Shipment, $this> */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
