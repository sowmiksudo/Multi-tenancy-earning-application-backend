<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('applets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('app_id')->unique()->comment('This is the X-App-ID');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->json('config')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('applets');
    }
};
