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
        Schema::create('user_relationship_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade'); // who sent the request
            $table->foreignId('target_id')->constrained('users')->onDelete('cascade');    // who received the request
            $table->enum('type', ['supervisor', 'student']); // what kind of relationship is being requested
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
            // $table->softDeletes(); this should be commented because its create a lot of bugs

            $table->unique(['requester_id', 'target_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_relationship_requests');
    }
};
