<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();
        $slug = Str::slug($name).'-'.fake()->unique()->numerify('###');

        return [
            'name' => $name,
            'slug' => $slug,
            'subdomain' => $slug,
            'preset' => 'custom',
            'locale' => 'en',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'status' => 'active',
            'is_demo' => false,
        ];
    }
}
