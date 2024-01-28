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
        Schema::create('purchasing_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code_transaction');
            $table->foreignId('company_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code_employee');
            $table->foreignId('vendor_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('date_transaction');
            $table->enum('status', ['ordered', 'pending','completed'])->default('ordered');
            $table->text('desc')->nullable();
            $table->integer('total_pay')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchasing_orders');
    }
};
