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
        Schema::create('chat_message_reactions', function (Blueprint $table) {
            $table->id();

            // Which message this reaction belongs to
            $table->foreignId('message_id')->constrained('chat_messages')->onDelete('cascade');

            // Who reacted
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Reaction type, e.g. emoji code or name like "like", "❤️", "😂"
            $table->string('reaction');

            $table->timestamps();

            // Prevent duplicate reactions by same user on same message with same reaction
            $table->unique(['message_id', 'user_id', 'reaction']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_message_reactions');
    }   
};
