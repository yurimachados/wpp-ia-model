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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('content');
            $table->string('caption');
            $table->string('status');
            $table->string('type');
            $table->string('message_id')->unique();
            $table->string('reference_message_id')->nullable();
            $table->string('direction');
            $table->string('file_url')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->timestamps();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('reference_message_id')->references('message_id')->on('messages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
