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
        Schema::create('court_rates', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('day_of_week')->comment('2-8: Thứ 2-Chủ nhật');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('price_per_hour');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('court_rates');
    }
}; 