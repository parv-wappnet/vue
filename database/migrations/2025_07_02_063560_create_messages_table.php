<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * This table stores all chat messages for both private and group conversations.
     * Each message can be a text, file, image, audio, or video.
     * Supports soft-deletes and edit tracking.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id(); // Primary key

            $table->foreignId('sender_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Sender user ID');

            $table->foreignId('conversation_id')
                ->constrained('conversations')
                ->onDelete('cascade')
                ->comment('Conversation ID this message belongs to');

            $table->text('content')
                ->comment('Message content, required for all types');

            $table->enum('type', ['text', 'image', 'file', 'audio', 'video'])
                ->default('text')
                ->comment('Type of message (text/image/file/etc)');

            $table->string('file_url')->nullable()->comment('File storage URL if type is not text');
            $table->string('file_name')->nullable()->comment('Original file name if uploaded');
            $table->bigInteger('file_size')->nullable()->comment('File size in bytes');

            $table->boolean('is_edited')->default(false)->comment('Indicates if message was edited');
            $table->timestamp('edited_at')->nullable()->comment('Timestamp when message was last edited');

            $table->boolean('is_deleted')->default(false)->comment('Indicates if message is soft-deleted');
            $table->timestamp('deleted_at')->nullable()->comment('Timestamp when message was soft-deleted');

            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the `messages` table entirely.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
