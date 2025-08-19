<?php

// 2023_01_01_000011_create_tasks_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applet_id')->constrained();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('category')->nullable();
            $table->decimal('task_limit', 10, 2)->default(0.00);
            $table->enum('limit_type', ['daily', 'monthly'])->default('daily');
            $table->decimal('reward', 10, 2);
            $table->json('requirements')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
