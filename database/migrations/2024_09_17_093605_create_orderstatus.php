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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // For the status name
            $table->string('color')->nullable(); // Color field for the status
            $table->text('description')->nullable(); // Description of the status
            $table->boolean('lock_completed_orders')->default(false); // Checkbox for locking completed orders
            $table->boolean('change_status_on_revision')->default(false); // Checkbox for changing status on revision request
            $table->boolean('enable_ratings')->default(false); // Checkbox for enabling ratings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderstatus');
    }
};
