<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsnLine extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'asn_id',
        'product_id',
        'purchase_order_line_id',
        'qty_expected',
        'line_number',
    ];

    protected function casts(): array
    {
        return [
            'qty_expected' => 'decimal:3',
            'line_number' => 'integer',
        ];
    }

    /** @return BelongsTo<Asn, $this> */
    public function asn(): BelongsTo
    {
        return $this->belongsTo(Asn::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<PurchaseOrderLine, $this> */
    public function purchaseOrderLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderLine::class);
    }
}
