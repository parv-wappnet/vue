<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * This creates the `groups` table to support group chat functionality.
     * Each group can have a list of admin users and a list of all member users (stored as JSON).
     * A group must be created by a registered user (`created_by`).
     */
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id(); // Primary key: group ID

            $table->json('admins')
                ->comment('JSON array of user IDs with admin role in this group');

            $table->json('members')
                ->comment('JSON array of all user IDs who are members of this group');

            $table->text('description')
                ->nullable()
                ->comment('Optional group description or purpose');

            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User ID of the group creator (references users table)');

            $table->timestamps(); // Includes created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the `groups` table and removes all group data.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
