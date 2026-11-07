<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills_of_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('finished_product_id')->constrained('products')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->decimal('yield_qty', 14, 3)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('bom_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bill_of_materials_id')->constrained('bills_of_materials')->cascadeOnDelete();
            $table->foreignId('component_product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('qty', 14, 3);
            $table->unsignedInteger('line_number')->default(1);
            $table->timestamps();
        });

        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bill_of_materials_id')->constrained('bills_of_materials')->cascadeOnDelete();
            $table->foreignId('finished_product_id')->constrained('products')->cascadeOnDelete();
            $table->string('number');
            $table->string('status', 32)->default('draft');
            $table->decimal('qty_planned', 14, 3);
            $table->decimal('qty_built', 14, 3)->default(0);
            $table->foreignId('output_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
        });

        Schema::create('dock_doors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('status', 32)->default('available');
            $table->timestamps();
            $table->unique(['warehouse_id', 'code']);
        });

        Schema::create('dock_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dock_door_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number');
            $table->string('direction', 16)->default('inbound');
            $table->string('status', 32)->default('scheduled');
            $table->string('carrier_name')->nullable();
            $table->timestamp('scheduled_at');
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
        });

        Schema::create('quality_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number');
            $table->string('status', 32)->default('open');
            $table->string('reason');
            $table->decimal('qty', 14, 3)->nullable();
            $table->foreignId('released_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
        });

        Schema::create('non_conformances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->string('status', 32)->default('open');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('severity')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
        });

        Schema::create('equipment_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('type', 32)->default('forklift');
            $table->string('status', 32)->default('available');
            $table->date('next_maintenance_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('equipment_checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('checked_out_at');
            $table->timestamp('checked_in_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('safety_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number');
            $table->string('severity', 32)->default('low');
            $table->string('status', 32)->default('open');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_incidents');
        Schema::dropIfExists('equipment_checkouts');
        Schema::dropIfExists('equipment_assets');
        Schema::dropIfExists('non_conformances');
        Schema::dropIfExists('quality_holds');
        Schema::dropIfExists('dock_appointments');
        Schema::dropIfExists('dock_doors');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('bom_lines');
        Schema::dropIfExists('bills_of_materials');
    }
};
