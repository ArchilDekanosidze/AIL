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
        Schema::create('chat_message_attachments', function (Blueprint $table) {
            $table->id();

            // Link to message
            $table->foreignId('message_id')->constrained('chat_messages')->onDelete('cascade');

            $table->string('file_name')->nullable();
            // Attachment type: image, video, voice, file etc.
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            // File path or URL to the stored attachment
            $table->string('file_path');

            // Optional metadata like size, duration, thumbnail etc.
            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_message_attachments');
    }
};
