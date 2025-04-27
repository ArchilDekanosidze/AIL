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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');  // Foreign key to questions table
            $table->foreignId('user_id')->constrained()->onDelete('cascade');      // Foreign key to users table
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');  // For replies to comments (self-reference)
            $table->text('body');
            $table->foreignId('original_id')->nullable()->constrained('comments')->onDelete('set null'); // For original comment reference
            $table->foreignId('original_user_id')->nullable()->constrained('users')->onDelete('set null'); // For original user reference
            $table->foreignId('best_reply_id')->nullable()->constrained('comments')->onDelete('set null'); // For best reply to the comment
            $table->integer('score')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
