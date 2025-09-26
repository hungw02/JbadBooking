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
        Schema::create('storages', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->text('product_name');
            $table->text('quantity');
            $table->decimal('total_price', 10, 2);
            $table->enum('transaction_type', ['sale', 'rent'])->default('rent');
            $table->enum('status', ['returned', 'not_returned', 'completed']);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storages');
    }
}; 