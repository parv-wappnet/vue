<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->paragraph(),
            'type' => 'text',
            'file_url' => null,
            'file_name' => null,
            'file_size' => null,
            'is_edited' => false,
            'edited_at' => null,
            'is_deleted' => false,
            'deleted_at' => null,
        ];
    }

    /**
     * Indicate that the message is a file.
     */
    public function file(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => $this->faker->randomElement(['image', 'file', 'audio', 'video']),
            'file_url' => $this->faker->url(),
            'file_name' => $this->faker->fileName(),
            'file_size' => $this->faker->numberBetween(1000, 10000000),
            'content' => null,
        ]);
    }

    /**
     * Indicate that the message is edited.
     */
    public function edited(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_edited' => true,
            'edited_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ]);
    }
}
