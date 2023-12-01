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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->string('brand');
            $table->string('model')->nullable();
            $table->integer('year');
            $table->integer('mileage');
            $table->string('condition');
            $table->string('km_driven');
            $table->string('color');
            $table->string('used_time');
            $table->string('fuel_type');
            $table->string('owner');
            $table->string('transmission_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
