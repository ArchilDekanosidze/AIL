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
        Schema::create('user_badge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // foreign key for user
            $table->foreignId('tag_id')->nullable()->constrained('tags')->onDelete('cascade'); // optional foreign key for tag
            $table->string('badge')->nullable(); // Badge name (e.g., Bronze, Silver, Gold)
            $table->bigInteger('score')->default(0); // Score for the area (could be computed)
            $table->timestamps();
            $table->softDeletes(); // Soft delete support
            
            // Optionally, you can add a unique constraint on `user_id` and `tag_id` to ensure each user can only have one badge per tag.
            $table->unique(['user_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_badge');
    }
};
