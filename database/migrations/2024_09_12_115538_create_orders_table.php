<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id'); // Ensure this is unsigned
            $table->unsignedBigInteger('client_id'); // Make sure the same goes for client_id
            $table->text('note')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });

        Schema::create('order_team_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Reference to the order
            $table->foreignId('team_member_id')->constrained('team_members')->onDelete('cascade'); // Assigned team members
        });

        Schema::create('order_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Reference to the order
            $table->text('action'); // Description of the change (e.g., order status changed, note updated)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_history');
        Schema::dropIfExists('order_team_member');
        Schema::dropIfExists('orders');
    }
}
