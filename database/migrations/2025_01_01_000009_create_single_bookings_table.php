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
        Schema::create('single_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('court_id');
            $table->unsignedBigInteger('customer_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('payment_type', ['deposit', 'full']);
            $table->enum('payment_method', ['vnpay', 'wallet']);
            $table->unsignedBigInteger('total_price');
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->unsignedInteger('discount_percent')->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->dateTime('cancel_time')->nullable();
            $table->timestamps();

            $table->foreign('court_id')->references('id')->on('courts')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('single_bookings');
    }
}; 