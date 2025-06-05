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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();

            // Which conversation this message belongs to
            $table->foreignId('conversation_id')->constrained('chat_conversations')->onDelete('cascade');

            // Which user sent this message
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');

            // If this message is a reply to another message (self-reference)
            $table->foreignId('reply_to_message_id')->nullable()->constrained('chat_messages')->onDelete('set null');

            // Message type: text, image, video, voice, file, etc.
            $table->enum('type', ['text', 'image', 'video', 'voice', 'file'])->default('text');

            // The message content or the file path/url
            $table->text('content')->nullable();

            // Optional: file metadata for files, videos, voices (e.g., JSON)
            $table->json('metadata')->nullable();

            // Whether the message is edited or deleted
            $table->boolean('is_edited')->default(false);
            $table->boolean('is_deleted')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
