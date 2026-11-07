<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('slug');
            $table->string('timezone')->default('UTC');
            $table->string('status', 32)->default('active');
            $table->json('address')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('type', 32)->default('storage');
            $table->unsignedInteger('pick_sequence')->default(0);
            $table->json('restrictions')->nullable();
            $table->timestamps();
            $table->unique(['warehouse_id', 'code']);
            $table->index(['tenant_id', 'warehouse_id']);
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code');
            $table->string('barcode')->nullable();
            $table->string('type', 32)->default('storage');
            $table->string('aisle', 32)->nullable();
            $table->string('rack', 32)->nullable();
            $table->string('level', 32)->nullable();
            $table->string('bin', 32)->nullable();
            $table->unsignedInteger('pick_sequence')->default(0);
            $table->decimal('max_weight', 12, 3)->nullable();
            $table->decimal('max_volume', 12, 3)->nullable();
            $table->unsignedInteger('max_pallets')->nullable();
            $table->unsignedInteger('max_units')->nullable();
            $table->boolean('is_pick_face')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->string('status', 32)->default('active');
            $table->json('restrictions')->nullable();
            $table->json('dimensions')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['warehouse_id', 'code']);
            $table->index(['tenant_id', 'warehouse_id', 'type']);
            $table->index(['tenant_id', 'barcode']);
            $table->index(['warehouse_id', 'pick_sequence']);
        });

        Schema::create('lpns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_lpn_id')->nullable()->constrained('lpns')->nullOnDelete();
            $table->string('code');
            $table->string('type', 32)->default('pallet');
            $table->string('status', 32)->default('available');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'warehouse_id', 'status']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('sku');
            $table->string('name');
            $table->string('slug');
            $table->string('status', 32)->default('active');
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('category')->nullable();
            $table->string('abc_class', 1)->nullable();
            $table->boolean('track_lot')->default(false);
            $table->boolean('track_serial')->default(false);
            $table->boolean('track_expiry')->default(false);
            $table->string('shelf_life_strategy', 16)->default('FEFO');
            $table->decimal('weight', 12, 3)->nullable();
            $table->decimal('length', 12, 3)->nullable();
            $table->decimal('width', 12, 3)->nullable();
            $table->decimal('height', 12, 3)->nullable();
            $table->string('hs_code')->nullable();
            $table->string('country_of_origin', 2)->nullable();
            $table->decimal('reorder_point', 14, 3)->nullable();
            $table->decimal('reorder_qty', 14, 3)->nullable();
            $table->decimal('min_qty', 14, 3)->nullable();
            $table->decimal('max_qty', 14, 3)->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'sku']);
            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'category']);
        });

        Schema::create('product_barcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('barcode');
            $table->string('type', 32)->default('CODE128');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['tenant_id', 'barcode']);
            $table->index(['product_id']);
        });

        Schema::create('uoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->decimal('factor_to_base', 14, 6)->default(1);
            $table->boolean('is_base')->default(false);
            $table->timestamps();
            $table->unique(['product_id', 'code']);
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status', 32)->default('active');
            $table->json('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('status', 32)->default('active');
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('uoms');
        Schema::dropIfExists('product_barcodes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('lpns');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('warehouses');
    }
};
