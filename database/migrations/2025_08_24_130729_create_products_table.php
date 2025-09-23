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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Product_ID
            $table->string('name');
            $table->string('category')->nullable();
            $table->integer('inventory')->default(0);
            $table->decimal('price', 10, 2);
            $table->enum('status', ['Instock', 'Out of Stock'])->default('Instock');
            $table->integer('sold_pieces')->default(0);
            $table->string('image')->nullable(); // For product image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
