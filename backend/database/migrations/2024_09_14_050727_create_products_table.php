<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); 
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->enum('status', ['available', 'out_of_stock'])->default('available');
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}

