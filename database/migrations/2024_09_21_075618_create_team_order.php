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
        Schema::create('order_team_member', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('team_member_id');
            $table->timestamps();

            // Ensure order_id and team_member_id have foreign keys to correct tables
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('team_member_id')->references('id')->on('team_members')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_team_member');
    }
};
