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
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['user_id']);
            $table->index(['type']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['ends_at']);
        });
        Schema::table('likes', function (Blueprint $table) {
            $table->index(['from_user_id','to_user_id']);
        });
        Schema::table('conversations', function (Blueprint $table) {
            $table->index(['match_id']);
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['conversation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payments_user_id_index']);
            $table->dropIndex(['payments_type_index']);
            $table->dropIndex(['payments_status_index']);
            $table->dropIndex(['payments_created_at_index']);
        });
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropIndex(['user_subscriptions_user_id_index']);
            $table->dropIndex(['user_subscriptions_status_index']);
            $table->dropIndex(['user_subscriptions_ends_at_index']);
        });
        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex(['likes_from_user_id_to_user_id_index']);
        });
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex(['conversations_match_id_index']);
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['messages_conversation_id_index']);
        });
    }
};
