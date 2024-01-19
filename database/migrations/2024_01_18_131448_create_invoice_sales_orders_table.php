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
        Schema::create('invoice_sales_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('detail_sales_order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('price');
            $table->boolean('is_completed')->default(true);
            $table->integer('pay');
            $table->text('desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_sales_orders');
    }
};
