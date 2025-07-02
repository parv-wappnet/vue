<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * This creates the `conversations` table to support both private and group chats.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id(); // Primary key: id (bigint unsigned auto-increment)

            $table->string('name')->nullable(); // Optional conversation name (for group chats)
            $table->enum('type', ['private', 'group'])->default('private'); // Chat type

            $table->text('description')->nullable(); // Optional description (for group chats)

            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            // Who created the conversation (user)

            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade');
            // Receiver for private chat (null for group chat)

            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('cascade');
            // Group ID if it's a group chat (null for private)

            $table->boolean('is_active')->default(true); // Flag for soft-deactivation

            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * This drops the `conversations` table.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
