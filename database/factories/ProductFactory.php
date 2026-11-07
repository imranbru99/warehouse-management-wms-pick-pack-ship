<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\Tenant;
use App\Support\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'tenant_id' => fn () => app(TenantContext::class)->id() ?? Tenant::factory(),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-########')),
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'status' => 'active',
            'description' => fake()->optional()->sentence(),
            'brand' => fake()->optional()->company(),
            'category' => fake()->optional()->word(),
            'track_lot' => false,
            'track_serial' => false,
            'track_expiry' => false,
            'shelf_life_strategy' => 'FEFO',
        ];
    }
}
