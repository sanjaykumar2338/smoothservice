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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('billing_address')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('company')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->boolean('send_welcome_email')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
