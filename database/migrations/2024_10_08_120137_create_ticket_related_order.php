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
        Schema::table('tickets', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['related_order_id']);

            // Drop the column after the constraint has been dropped
            $table->dropColumn('related_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Add the column back
            $table->foreignId('related_order_id')->nullable()->constrained('orders')->onDelete('set null');
        });

    }
};
