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
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('import_price');
            $table->unsignedBigInteger('selling_price');
            $table->integer('sale')->default(0);
            $table->string('image')->nullable();
            $table->unsignedInteger('quantity');
            $table->enum('status', ['available', 'out_of_stock'])->default('available');
            $table->enum('type', ['sale', 'rent'])->default('sale');
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