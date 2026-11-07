<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('locale', 10)->default('en')->after('password');
            $table->string('timezone')->nullable()->after('locale');
            $table->boolean('is_platform_admin')->default(false)->after('timezone');
            $table->boolean('is_demo_user')->default(false)->after('is_platform_admin');
            $table->string('portal_type', 32)->nullable()->after('is_demo_user');
            $table->text('two_factor_secret')->nullable()->after('portal_type');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            $table->json('preferences')->nullable();
            $table->index(['tenant_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn([
                'locale',
                'timezone',
                'is_platform_admin',
                'is_demo_user',
                'portal_type',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
                'preferences',
            ]);
        });
    }
};
