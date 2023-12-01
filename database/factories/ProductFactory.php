<?php

namespace Database\Factories;

use App\Models\product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = product::class;
    public function definition(): array
    {
        return [
            'user_id'     => 1, // Replace 1 and 10 with the range of user IDs
            'category_id' => 2,  // Replace 1 and 5 with the range of category IDs
            'pname'       => $this->faker->word,
            'description' => $this->faker->paragraph,
            'price'       => $this->faker->numberBetween(1000, 10000) // Replace 10 and 100 with the range of prices
        ];
    }
}
