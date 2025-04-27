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
        Schema::create('user_category_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_question_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->float('level')->default(1);
            $table->float('target_level')->default(100);
            $table->integer('number_to_change_level')->default(10);
            $table->timestamps();
            
            $table->unique(['user_id', 'category_question_id']);            
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_category_question');
    }
};
