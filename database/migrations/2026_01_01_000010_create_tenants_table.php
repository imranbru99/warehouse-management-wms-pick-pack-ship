<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->string('preset')->default('custom');
            $table->string('locale', 10)->default('en');
            $table->string('timezone')->default('UTC');
            $table->string('currency', 3)->default('USD');
            $table->string('unit_system', 16)->default('metric');
            $table->string('document_locale', 10)->default('en');
            $table->string('status', 32)->default('active')->index();
            $table->boolean('is_demo')->default(false);
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
