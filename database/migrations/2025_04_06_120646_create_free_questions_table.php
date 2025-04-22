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
        Schema::create('free_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->text('head')->nullable();
            $table->text('body')->nullable();
            $table->bigInteger('best_reply_id')->nullable();
            $table->integer('score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_questions');
    }
};
