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
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            
            // Conversation title (for groups/channels)
            $table->string('title')->nullable();
            
            // Conversation type: simple, group, channel
            $table->enum('type', ['private', 'group', 'channel'])->default('simple');
            $table->boolean('is_private')->default(true); 
            $table->string('slug')->unique()->nullable();
            
            // Optional: owner/admin of conversation (nullable for simple)
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Metadata JSON (optional extra info)
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
