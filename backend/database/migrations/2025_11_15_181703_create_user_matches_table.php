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
        Schema::create('user_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_a')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id_b')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id_a', 'user_id_b']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_matches');
    }
};
