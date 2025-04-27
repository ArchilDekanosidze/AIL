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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->nullable()->constrained('tags')->onDelete('set null'); // tag_id references tags table
            $table->foreignId('category_question_id')->constrained('category_questions')->onDelete('cascade'); // category_question_id references category_questions table
            $table->integer("answer")->nullable();
            $table->float('percentage', 15, 8);
            $table->integer('count')->default(100);
            $table->string('type')->default('test');
            $table->boolean('isfree')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
