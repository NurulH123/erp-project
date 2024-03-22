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
        Schema::create('coa_additions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id')->constrained('c_o_a_s')->cascadeOnUpdate()->cascadeOnDelete();
            $table->morphs('companiable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_additions');
    }
};
