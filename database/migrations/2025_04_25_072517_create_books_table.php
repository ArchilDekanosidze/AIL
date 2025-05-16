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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("image")->nullable();
            $table->string("fileName")->nullable();
            $table->text("fileUrl")->nullable();
            $table->string("fileSize")->nullable();
            $table->string("year");
            $table->string("code");
            $table->string("chap_url");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
