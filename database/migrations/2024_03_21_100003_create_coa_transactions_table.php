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
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('companiable');
            $table->morphs('invoiceable');
            $table->enum('type', ['cash, bank']);
            $table->foreignId('transaction_category')->constrained('c_o_a_s')->nullOnDelete();
            $table->foreignId('fund')->constrained('c_o_a_s')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_transactions');
    }
};
