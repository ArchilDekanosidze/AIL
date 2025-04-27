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
        Schema::create('user_free_badge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('free_tag_id')->nullable()->constrained('free_tags')->onDelete('cascade'); // Nullable foreign key
            $table->string('badge')->nullable();  // E.g., Bronze, Silver, Gold, etc.
            $table->bigInteger('score')->default(0);  // Score for the area (could be a computed score)
            $table->timestamps();        
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_free_badge');
    }
};
