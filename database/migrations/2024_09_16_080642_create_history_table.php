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
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('cascade'); // Tracks order updates
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade'); // Tracks client-related actions
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Tracks the user/admin who performed the action
            $table->string('action_type'); // Action types like 'order_update', 'message_to_client', 'message_to_team', 'note_saved'
            $table->text('action_details')->nullable(); // Additional details (like the message content)
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};
