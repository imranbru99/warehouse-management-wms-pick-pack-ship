<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptLine extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'receipt_id',
        'product_id',
        'purchase_order_line_id',
        'qty',
        'lot_code',
        'serial_code',
        'lpn_id',
        'line_number',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3',
            'line_number' => 'integer',
        ];
    }

    /** @return BelongsTo<Receipt, $this> */
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
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

    /** @return BelongsTo<Lpn, $this> */
    public function lpn(): BelongsTo
    {
        return $this->belongsTo(Lpn::class);
    }
}
