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
            $table->bigInteger('category_question_id')->unsigned(); 
            $table->string("front");
            $table->string("back");
            $table->string("p1");
            $table->string("p2");
            $table->string("p3");
            $table->string("p4");
            $table->integer("answer");
            $table->float('percentage', 15, 8);
            $table->integer('count');
            $table->boolean('isfree')->default(0);
            $table->timestamps();
            $table->foreign('category_question_id')->references('id')->on('category_questions')->onDelete('cascade');
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
