<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rma_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number');
            $table->string('status', 32)->default('draft');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'number']);
            $table->index(['tenant_id', 'warehouse_id', 'status']);
        });

        Schema::create('rma_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rma_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_order_line_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('line_number')->default(1);
            $table->decimal('qty_requested', 14, 3);
            $table->decimal('qty_received', 14, 3)->default(0);
            $table->decimal('qty_restocked', 14, 3)->default(0);
            $table->string('disposition', 32)->default('pending');
            $table->text('condition_notes')->nullable();
            $table->timestamps();
            $table->index(['rma_request_id', 'line_number']);
        });

        Schema::create('return_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rma_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->string('status', 32)->default('posted');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
            $table->index(['rma_request_id', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_receipts');
        Schema::dropIfExists('rma_lines');
        Schema::dropIfExists('rma_requests');
    }
};
