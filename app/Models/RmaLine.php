<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Returns\RmaLineDisposition;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RmaLine extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'rma_request_id',
        'product_id',
        'sales_order_line_id',
        'line_number',
        'qty_requested',
        'qty_received',
        'qty_restocked',
        'disposition',
        'condition_notes',
    ];

    protected function casts(): array
    {
        return [
            'disposition' => RmaLineDisposition::class,
        ];
    }

    /** @return BelongsTo<RmaRequest, $this> */
    public function rmaRequest(): BelongsTo
    {
        return $this->belongsTo(RmaRequest::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<SalesOrderLine, $this> */
    public function salesOrderLine(): BelongsTo
    {
        return $this->belongsTo(SalesOrderLine::class);
    }
}
