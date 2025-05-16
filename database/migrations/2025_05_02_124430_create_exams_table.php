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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_exam_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title'); 
            $table->text('url'); 
            $table->unsignedTinyInteger('has_answer')->default(0); 
            $table->string('school_type')->nullable(); 
            $table->string('state')->nullable(); 
            $table->string('city')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
