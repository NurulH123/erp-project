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
            $table->string('tag');
            $table->text('description')->nullable();
            $table->string('model');
            $table->bigInteger('price');
            $table->integer('quantity');
            $table->integer('minimum_quantity')->nullable();
            $table->string('image')->nullable();
            $table->string('product_status');
            $table->integer('length')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->enum('length_class',['Centimeter','Millimeter','Inch'])->default('Centimeter');
            $table->integer('weight')->nullable();
            $table->enum('weight_class',['Kilogram','Gram','Pound','Ounce'])->default('Gram');
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
