<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_balances', function (Blueprint $table) {
            if (! Schema::hasColumn('stock_balances', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('status');
            }
        });

        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number');
            $table->string('external_ref')->nullable();
            $table->string('status', 32)->default('draft');
            $table->unsignedTinyInteger('priority')->default(5);
            $table->timestamp('ordered_at')->nullable();
            $table->date('required_ship_date')->nullable();
            $table->json('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'number']);
            $table->index(['tenant_id', 'warehouse_id', 'status']);
        });

        Schema::create('sales_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('line_number')->default(1);
            $table->string('sku');
            $table->decimal('qty_ordered', 14, 3);
            $table->decimal('qty_allocated', 14, 3)->default(0);
            $table->decimal('qty_picked', 14, 3)->default(0);
            $table->decimal('qty_shipped', 14, 3)->default(0);
            $table->decimal('unit_price', 14, 4)->nullable();
            $table->string('status', 32)->default('open');
            $table->timestamps();
            $table->index(['sales_order_id', 'line_number']);
        });

        Schema::create('waves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('status', 32)->default('planned');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->unique(['warehouse_id', 'code']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_order_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_balance_id')->nullable()->constrained()->nullOnDelete();
            $table->string('lot_code')->default('');
            $table->decimal('qty', 14, 3);
            $table->string('status', 32)->default('allocated');
            $table->string('strategy', 32)->default('fifo');
            $table->timestamps();
            $table->index(['sales_order_line_id', 'status']);
        });

        Schema::create('pick_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wave_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('allocation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('qty', 14, 3);
            $table->unsignedInteger('pick_sequence')->default(0);
            $table->string('status', 32)->default('pending');
            $table->timestamps();
            $table->index(['wave_id', 'pick_sequence']);
            $table->index(['sales_order_id', 'status']);
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->string('service')->nullable();
            $table->string('status', 32)->default('draft');
            $table->timestamp('shipped_at')->nullable();
            $table->json('ship_to')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
            $table->index(['tenant_id', 'tracking_number']);
        });

        Schema::create('cartons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->decimal('length', 12, 3)->nullable();
            $table->decimal('width', 12, 3)->nullable();
            $table->decimal('height', 12, 3)->nullable();
            $table->decimal('weight', 12, 3)->nullable();
            $table->string('status', 32)->default('open');
            $table->json('contents')->nullable();
            $table->timestamps();
            $table->unique(['shipment_id', 'code']);
        });

        Schema::create('shipment_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('carton_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tracking_number')->nullable();
            $table->decimal('weight', 12, 3)->nullable();
            $table->timestamps();
        });

        Schema::create('loads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('carrier')->nullable();
            $table->string('status', 32)->default('planned');
            $table->timestamp('departed_at')->nullable();
            $table->timestamps();
            $table->unique(['warehouse_id', 'code']);
        });

        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->timestamp('occurred_at');
            $table->string('status', 64);
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            $table->index(['shipment_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
        Schema::dropIfExists('loads');
        Schema::dropIfExists('shipment_packages');
        Schema::dropIfExists('cartons');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('pick_tasks');
        Schema::dropIfExists('allocations');
        Schema::dropIfExists('waves');
        Schema::dropIfExists('sales_order_lines');
        Schema::dropIfExists('sales_orders');

        Schema::table('stock_balances', function (Blueprint $table) {
            if (Schema::hasColumn('stock_balances', 'expiry_date')) {
                $table->dropColumn('expiry_date');
            }
        });
    }
};
