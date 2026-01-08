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
        Schema::table('messages', function (Blueprint $table) {
            // Index for fetching conversation between two users
            $table->index(['sender_id', 'receiver_id'], 'messages_conversation_idx');
            
            // Index for marking messages as read
            $table->index(['sender_id', 'receiver_id', 'read'], 'messages_read_status_idx');
            
            // Index for ordering by created_at
            $table->index('created_at', 'messages_created_at_idx');
            
            // Composite index for efficient message polling
            $table->index(['sender_id', 'receiver_id', 'id'], 'messages_polling_idx');
        });

        // Add indexes to push_subscriptions table
        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->index('user_id', 'push_subscriptions_user_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_conversation_idx');
            $table->dropIndex('messages_read_status_idx');
            $table->dropIndex('messages_created_at_idx');
            $table->dropIndex('messages_polling_idx');
        });

        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->dropIndex('push_subscriptions_user_idx');
        });
    }
};
