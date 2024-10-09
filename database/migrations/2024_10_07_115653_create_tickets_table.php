<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            
            // Client reference (assuming clients table exists)
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Subject of the ticket
            $table->string('ticket_no');
            $table->string('subject');
            $table->integer('order_id');
            $table->integer('user_id');

            // Related order reference (assuming orders table exists)
            $table->foreignId('related_order_id')->nullable()->constrained('orders')->onDelete('set null');

            // Message field
            $table->text('message');

            $table->timestamps();
        });

        // Create the pivot table for CC (Carbon Copy) multiple users for each ticket
        Schema::create('ticket_user', function (Blueprint $table) {
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_user');
        Schema::dropIfExists('tickets');
    }
}
