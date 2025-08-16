<?php

// 2023_01_01_000005_create_tenant_applets_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenant_applets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('applet_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->timestamps();

            $table->unique(['tenant_id', 'applet_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenant_applets');
    }
};
