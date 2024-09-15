<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade'); // Reference to the order
            $table->string('name'); // Task name
            $table->text('description')->nullable(); // Task description
            $table->foreignId('assigned_to')->constrained('team_members')->onDelete('cascade'); // Assigned team member
            $table->dateTime('due_date'); // Due date
            $table->string('due_from_previous')->nullable(); // Previous task reference
            $table->string('due_type')->nullable(); // Previous task reference
            $table->integer('due_period_value')->nullable(); // Value for the period (e.g., 3)
            $table->string('due_period_type')->nullable(); // Type of period (e.g., days, hours)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}