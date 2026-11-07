<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Inbound\QcInspectionStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QcInspection extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'warehouse_id',
        'receipt_id',
        'receipt_line_id',
        'product_id',
        'status',
        'result',
        'qty_inspected',
        'qty_failed',
        'inspector_id',
        'inspected_at',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => QcInspectionStatus::class,
            'qty_inspected' => 'decimal:3',
            'qty_failed' => 'decimal:3',
            'inspected_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    /** @return BelongsTo<Warehouse, $this> */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /** @return BelongsTo<Receipt, $this> */
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    /** @return BelongsTo<ReceiptLine, $this> */
    public function receiptLine(): BelongsTo
    {
        return $this->belongsTo(ReceiptLine::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<User, $this> */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}
