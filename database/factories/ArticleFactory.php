<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(6, true);

        return [
            'user_id' => User::query()->inRandomOrder()->value('id') ?? 1,
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->paragraph(),
            'content' => implode("\n\n", $this->faker->paragraphs(5)),
            'featured_image' => 'https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?auto=format&fit=crop&w=1200&q=80',
            'status' => 'published',
            'is_featured' => false,
            'published_at' => now()->subDays(rand(1, 30)),
            'meta_title' => $title,
            'meta_description' => $this->faker->sentence(12),
            'reading_time' => rand(4, 9),
        ];
    }
}
