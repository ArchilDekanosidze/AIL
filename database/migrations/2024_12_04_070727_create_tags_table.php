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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();    
            $table->string('slug')->unique();    
            $table->text('description')->nullable(); 
            $table->bigInteger('bronz1');
            $table->bigInteger('bronz2');
            $table->bigInteger('bronz3');
            $table->bigInteger('silver1');
            $table->bigInteger('silver2');
            $table->bigInteger('silver3');
            $table->bigInteger('gold1');
            $table->bigInteger('gold2');
            $table->bigInteger('gold3');
            $table->bigInteger('platinum1');
            $table->bigInteger('platinum2');
            $table->bigInteger('platinum3');
            $table->bigInteger('dimond1');
            $table->bigInteger('dimond2');
            $table->bigInteger('dimond3');
            $table->bigInteger('legendary1');
            $table->bigInteger('legendary2');
            $table->bigInteger('legendary3');      
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
