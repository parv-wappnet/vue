<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->optional()->sentence(3),
            'type' => $this->faker->randomElement(['private', 'group']),
            'description' => $this->faker->optional()->paragraph(),
            'created_by' => User::factory(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the conversation is private.
     */
    public function private(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'private',
            'name' => null,
        ]);
    }

    /**
     * Indicate that the conversation is a group.
     */
    public function group(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'group',
            'name' => $this->faker->sentence(3),
        ]);
    }
}
