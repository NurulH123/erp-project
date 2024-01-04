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
            $table->foreignId('company_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code_product');
            $table->string('name');
            $table->string('type_zat')->nullable();
            $table->foreignId('unit_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('category_product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('price');
            $table->string('photo')->nullable();
            $table->boolean('status')->default(true)->comment('Active|Inactive');
            $table->text('desc')->nullable();
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
