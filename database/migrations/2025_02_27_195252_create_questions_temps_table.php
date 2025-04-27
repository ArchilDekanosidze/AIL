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
        Schema::create('questions_temps', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_question_id')->unsigned(); 
            $table->longText("front");
            $table->longText("back");
            $table->longText("p1")->nullable();
            $table->longText("p2")->nullable();
            $table->longText("p3")->nullable();
            $table->longText("p4")->nullable();
            $table->integer("answer")->nullable();
            $table->float('percentage', 15, 8);
            $table->integer('count')->default(100);
            $table->string('type')->default('test');
            $table->boolean('isfree')->default(0);
            $table->timestamps();
            $table->foreign('category_question_id')->references('id')->on('category_questions')->onDelete('cascade');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions_temps');
    }
};
