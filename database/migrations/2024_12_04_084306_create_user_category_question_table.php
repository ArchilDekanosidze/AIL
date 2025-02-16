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
            $table->text('level_history')->nullable();
            $table->time("level_history_time")->nullable();
            $table->text('answer_history')->nullable();            
            $table->int('number_to_change_level')->default(10);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_question');
    }
};
