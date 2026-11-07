<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('price_monthly_cents')->default(0);
            $table->unsignedInteger('price_yearly_cents')->default(0);
            $table->unsignedInteger('seat_limit')->nullable();
            $table->unsignedInteger('warehouse_limit')->nullable();
            $table->unsignedInteger('order_limit')->nullable();
            $table->json('module_keys')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('status', 32)->default('trialing');
            $table->string('billing_cycle', 16)->default('monthly');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_ends_at')->nullable();
            $table->string('gateway', 32)->nullable();
            $table->string('gateway_subscription_id')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->string('secret');
            $table->json('events')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('webhook_id')->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->json('payload')->nullable();
            $table->text('response_body')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->index(['webhook_id', 'created_at']);
        });

        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->string('trigger');
            $table->json('conditions')->nullable();
            $table->json('actions')->nullable();
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_code');
            $table->string('name');
            $table->string('status', 32)->default('active');
            $table->json('skills')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'employee_code']);
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->json('days_of_week')->nullable();
            $table->timestamps();
        });

        Schema::create('rate_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('rate_card_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rate_card_id')->constrained()->cascadeOnDelete();
            $table->string('service_code');
            $table->string('unit', 32)->default('each');
            $table->decimal('unit_price', 14, 4);
            $table->timestamps();
        });

        Schema::create('client_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rate_card_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number');
            $table->string('status', 32)->default('draft');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->json('lines')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'number']);
        });

        Schema::create('reorder_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('on_hand', 14, 3);
            $table->decimal('reorder_point', 14, 3);
            $table->decimal('suggested_qty', 14, 3);
            $table->string('status', 32)->default('open');
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('tenant_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('supplier_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'customer_id')) {
                $table->dropConstrainedForeignId('customer_id');
            }
            if (Schema::hasColumn('users', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }
        });

        Schema::dropIfExists('reorder_suggestions');
        Schema::dropIfExists('client_invoices');
        Schema::dropIfExists('rate_card_lines');
        Schema::dropIfExists('rate_cards');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('automation_rules');
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
