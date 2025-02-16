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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string("quiz_name")->nullable();
            $table->string("quiz_type")->default("online");
            $table->int("count")->default(0);
            $table->int("time")->default(0);
            $table->string("status")->default("created");
            $table->int("rightAnswers")->nullable();
            $table->int("wrongAnswers")->nullable();
            $table->int("notAnswers")->nullable();
            $table->int("finalPercentage")->nullable();

            

            $table->timestamp("started_at");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
