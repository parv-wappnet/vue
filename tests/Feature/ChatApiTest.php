<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'status',
                        'created_at',
                    ],
                    'token',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'status',
                    ],
                    'token',
                ],
                'message',
            ]);
    }

    public function test_user_can_create_conversation()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/conversations', [
            'type' => 'private',
            'user_ids' => [$otherUser->id],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'users',
                    'created_at',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('conversations', [
            'type' => 'private',
            'created_by' => $user->id,
        ]);
    }

    public function test_user_can_send_message()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create([
            'created_by' => $user->id,
        ]);

        $conversation->users()->attach($user->id, [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/conversations/{$conversation->id}/messages", [
            'content' => 'Hello, world!',
            'type' => 'text',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'content',
                    'type',
                    'user',
                    'created_at',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => 'Hello, world!',
            'type' => 'text',
        ]);
    }

    public function test_user_can_get_conversations()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create([
            'created_by' => $user->id,
        ]);

        $conversation->users()->attach($user->id, [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/conversations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'users',
                        'created_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    public function test_user_can_get_messages()
    {
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create([
            'created_by' => $user->id,
        ]);

        $conversation->users()->attach($user->id, [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        Message::factory()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/conversations/{$conversation->id}/messages");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'content',
                        'type',
                        'user',
                        'created_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }
}
