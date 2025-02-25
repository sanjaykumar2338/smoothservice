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
        Schema::create('user_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to users table
            $table->string('stripe_payment_intent')->unique(); // Store PaymentIntent ID
            $table->decimal('amount', 10, 2); // Store the fund amount
            $table->string('currency')->default('usd'); // Currency type
            $table->enum('status', ['pending', 'succeeded', 'failed'])->default('pending'); // Payment status
            $table->timestamps(); // Created_at & Updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_funds');
    }
};
