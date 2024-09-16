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
        Schema::table('client_replies', function (Blueprint $table) {
            $table->morphs('sender'); // This adds sender_id and sender_type columns
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_replies', function (Blueprint $table) {
            //
        });
    }
};
