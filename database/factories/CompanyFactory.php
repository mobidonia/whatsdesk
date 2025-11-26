<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'subdomain' => $this->faker->unique()->slug(),
            'active' => 1,
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'description' => $this->faker->text(200),
            'fee' => $this->faker->randomFloat(2, 0, 10),
            'static_fee' => $this->faker->randomFloat(2, 0, 5),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'views' => $this->faker->numberBetween(0, 1000),
            'whatsapp_phone' => $this->faker->phoneNumber(),
            'do_covertion' => 1,
            'currency' => 'USD',
        ];
    }
}
