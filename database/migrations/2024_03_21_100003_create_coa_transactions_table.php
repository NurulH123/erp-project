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
            $table->enum('type', ['cash','bank'])->nullable();
            $table->integer('nominal')->default(0);
            $table->foreignId('debet')->constrained('c_o_a_s')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('kredit')->constrained('c_o_a_s')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('desc')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
