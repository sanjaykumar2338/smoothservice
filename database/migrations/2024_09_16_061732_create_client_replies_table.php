<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('client_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            
            // Adding sender_id and sender_type for polymorphic relationship
            $table->morphs('sender'); // This creates sender_id and sender_type fields

            $table->text('message');
            $table->dateTime('scheduled_at')->nullable();
            $table->boolean('cancel_on_reply')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_replies');
    }
};
