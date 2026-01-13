<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WholesaleOrder>
 */
class WholesaleOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = Product::limit(5)->get();
        $items = $products->map(function ($product) {
            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'product_image' => is_array($product->images) ? ($product->images[0] ?? null) : null,
                'quantity' => fake()->numberBetween(10, 100),
                'notes' => fake()->optional()->sentence(),
            ];
        })->toArray();

        return [
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'tax_office' => fake()->city() . ' Vergi Dairesi',
            'tax_number' => fake()->numerify('##########'),
            'items' => $items,
            'additional_notes' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['pending', 'reviewing', 'approved', 'rejected', 'completed']),
            'admin_notes' => fake()->optional()->sentence(),
            'reviewed_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}





