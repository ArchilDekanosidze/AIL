<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_conversation_participants', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('conversation_id')->constrained('chat_conversations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Participant role in the conversation
            $table->enum('role', ['member', 'admin', 'owner'])->default('member');

            // Status flags
            $table->boolean('is_muted')->default(false);
            $table->boolean('is_banned')->default(false);

            // Optional last read message ID to track read status
            $table->foreignId('last_read_message_id')->nullable()->constrained('chat_messages')->onDelete('set null');

            $table->timestamp('joined_at')->useCurrent(); // Automatically set join time
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint so user can only join a conversation once
            $table->unique(['conversation_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversation_participants');
    }
};
