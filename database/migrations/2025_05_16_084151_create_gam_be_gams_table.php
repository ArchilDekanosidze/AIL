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
        Schema::create('gam_be_gams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_gam_be_gam_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title'); 
            $table->text('url'); 
            $table->text('file')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gam_be_gams');
    }
};
