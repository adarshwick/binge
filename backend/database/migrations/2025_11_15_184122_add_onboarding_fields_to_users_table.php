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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('onboarding_completed')->default(false);
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedSmallInteger('pref_min_age')->nullable();
            $table->unsignedSmallInteger('pref_max_age')->nullable();
            $table->unsignedSmallInteger('pref_distance_km')->nullable();
            $table->string('pref_gender')->nullable();
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['onboarding_completed','dob','gender','bio','pref_min_age','pref_max_age','pref_distance_km','pref_gender','lat','lng']);
        });
    }
};
