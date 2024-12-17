<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MainCharacters;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MainCharacter>
 */
class MainCharactersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = MainCharacters::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'story_id' => null,

        ];
    }
}
