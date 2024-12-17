<?php

namespace Database\Factories;
use App\Models\Category;
use App\Models\MainCharacters;
use App\Models\Story; 
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Story>
 */
class StoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
            'language'=> $this->faker->word(),
            'copyright'=>$this->faker->word(),
            'cover_image' => $this->faker->imageUrl(),
            'advertisement_image'=> $this->faker->imageUrl(),
            'content_type' => $this->faker->randomElement(['mature', 'general']),
            'status' => $this->faker->randomElement(['ongoing', 'completed']),
            'category_id' => Category::inRandomOrder()->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    public function configure()
{
    return $this->afterCreating(function (Story $story) {
        MainCharacters::factory()->count(3)->create(['story_id' => $story->id]);
    });
}
}
