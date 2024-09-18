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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status'); // Status name
            $table->text('description')->nullable(); 
            $table->string('color')->nullable(); // Optional color for the status indicator
            $table->boolean('lock_completed_orders')->default(false); // Checkbox field for locking completed orders
            $table->boolean('change_status_on_message')->default(false); // Checkbox for changing status on message/revision
            $table->boolean('enable_ratings')->default(false); // Checkbox for enabling ratings
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_statuses');
    }
};
