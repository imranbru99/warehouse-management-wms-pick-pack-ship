<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->string('status', 32)->default('draft');
            $table->timestamp('expected_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
            $table->index(['tenant_id', 'warehouse_id', 'status']);
        });

        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('qty_ordered', 14, 3);
            $table->decimal('qty_received', 14, 3)->default(0);
            $table->decimal('unit_cost', 14, 4)->nullable();
            $table->unsignedInteger('line_number')->default(1);
            $table->timestamps();
            $table->index(['purchase_order_id', 'product_id']);
        });

        Schema::create('asns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number');
            $table->string('status', 32)->default('expected');
            $table->timestamp('expected_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
        });

        Schema::create('asn_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asn_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_line_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('qty_expected', 14, 3);
            $table->unsignedInteger('line_number')->default(1);
            $table->timestamps();
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('asn_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number');
            $table->string('status', 32)->default('open');
            $table->foreignId('location_id')->constrained();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
            $table->index(['tenant_id', 'warehouse_id', 'status']);
        });

        Schema::create('receipt_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_line_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('qty', 14, 3);
            $table->string('lot_code')->default('');
            $table->string('serial_code')->default('');
            $table->foreignId('lpn_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('line_number')->default(1);
            $table->timestamps();
        });

        Schema::create('qc_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('receipt_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('receipt_line_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('status', 32)->default('pending');
            $table->string('result', 32)->nullable();
            $table->decimal('qty_inspected', 14, 3)->default(0);
            $table->decimal('qty_failed', 14, 3)->default(0);
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('inspected_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('warehouse_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32);
            $table->string('state', 32)->default('pending');
            $table->unsignedTinyInteger('priority')->default(50);
            $table->timestamp('sla_due_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('reference');
            $table->json('payload')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'warehouse_id', 'type', 'state']);
            $table->index(['tenant_id', 'assigned_to', 'state']);
        });

        Schema::create('putaway_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_task_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('receipt_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_location_id')->constrained('locations');
            $table->foreignId('to_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('qty', 14, 3);
            $table->string('lot_code')->default('');
            $table->string('serial_code')->default('');
            $table->foreignId('lpn_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 32)->default('pending');
            $table->unsignedTinyInteger('priority')->default(50);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'warehouse_id', 'status']);
        });

        Schema::create('stock_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained();
            $table->string('lot_code')->default('');
            $table->string('serial_code')->default('');
            $table->foreignId('lpn_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('qty_delta', 14, 3);
            $table->decimal('qty_before', 14, 3);
            $table->decimal('qty_after', 14, 3);
            $table->string('type', 32);
            $table->string('reason')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['tenant_id', 'product_id', 'location_id']);
            $table->index(['tenant_id', 'warehouse_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained();
            $table->string('lot_code')->default('');
            $table->string('serial_code')->default('');
            $table->foreignId('lpn_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 32)->default('available');
            $table->decimal('qty', 14, 3)->default(0);
            $table->decimal('reserved_qty', 14, 3)->default(0);
            $table->timestamps();
            $table->unique(
                ['tenant_id', 'warehouse_id', 'product_id', 'location_id', 'lot_code', 'serial_code', 'lpn_id', 'status'],
                'stock_balances_dimension_unique'
            );
            $table->index(['tenant_id', 'product_id', 'warehouse_id']);
        });

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->string('status', 32)->default('draft');
            $table->string('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
        });

        Schema::create('stock_adjustment_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_adjustment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained();
            $table->decimal('qty_delta', 14, 3);
            $table->string('lot_code')->default('');
            $table->string('serial_code')->default('');
            $table->foreignId('lpn_id')->nullable()->constrained()->nullOnDelete();
            $table->string('balance_status', 32)->default('available');
            $table->unsignedInteger('line_number')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_lines');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_balances');
        Schema::dropIfExists('stock_ledger_entries');
        Schema::dropIfExists('putaway_tasks');
        Schema::dropIfExists('warehouse_tasks');
        Schema::dropIfExists('qc_inspections');
        Schema::dropIfExists('receipt_lines');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('asn_lines');
        Schema::dropIfExists('asns');
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_orders');
    }
};
