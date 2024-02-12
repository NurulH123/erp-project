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
        Schema::create('invoice_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchasing_order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('detail_purchasing_order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('come')->nullable();
            $table->boolean('is_completed')->nullable();
            $table->integer('pay');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_purchase_orders');
    }
};
