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
        Schema::create('free_question_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key for user
            $table->foreignId('free_question_id')->nullable()->constrained('free_questions')->onDelete('cascade'); // Foreign key for free_question
            $table->foreignId('parent_id')->nullable()->constrained('free_question_comments')->onDelete('set null'); // Foreign key for parent comment (self-referencing)
            $table->text('body')->nullable();
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
        Schema::dropIfExists('free_question_comments');
    }
};
