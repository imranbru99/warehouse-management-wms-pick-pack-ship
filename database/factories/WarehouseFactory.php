<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\Warehouse;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'tenant_id' => fn () => app(TenantContext::class)->id() ?? Tenant::factory(),
            'code' => strtoupper(fake()->unique()->bothify('WH-###??')),
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'timezone' => 'UTC',
            'status' => 'active',
            'address' => null,
            'settings' => null,
            'is_default' => false,
        ];
    }
}
