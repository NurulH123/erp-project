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
        Schema::create('history_pos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchasing_order_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('date_accepted');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_pos');
    }
};
