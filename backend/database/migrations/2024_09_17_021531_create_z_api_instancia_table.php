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
        Schema::create('z_api_instances', function (Blueprint $table) {
            $table->id();
            $table->string('instance_id');
            $table->string('instance_token');
            $table->string('security_token');
            $table->string('phone');
            $table->enum('status', ['connected', 'desconnected'])->default('desconnected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('z_api_instances');
    }
};
