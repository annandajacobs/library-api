<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = $this->faker->numberBetween(1, 50);

        return [
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->unique()->isbn13(),
            'description' => $this->faker->paragraph(),
            'author_id' => Author::inRandomOrder()->first()?->id ?? Author::factory(),
            'genre' => $this->faker->randomElement([
                'Fiction', 'Non-fiction', 'Sci-Fi', 'Fantasy', 'Mistery', 'Romance'
            ]),
            'publushed_at' => $this->faker->date(),
            'total_copies' => $total,
            'available_copies' => $this->faker->numberBetween(0, $total),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'cover_image' => $this->faker->imageUrl(200, 300, 'books', true),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
