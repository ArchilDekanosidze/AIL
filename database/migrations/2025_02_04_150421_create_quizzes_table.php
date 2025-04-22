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
            $table->string("quiz_name")->nullable();
            $table->string("quiz_type")->default("online");
            $table->integer("count")->default(0);
            $table->integer("time")->default(0);
            $table->string("status")->default("created");
            $table->integer("rightAnswers")->nullable();
            $table->integer("wrongAnswers")->nullable();
            $table->integer("notAnswers")->nullable();
            $table->integer("finalPercentage")->nullable();

            

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
