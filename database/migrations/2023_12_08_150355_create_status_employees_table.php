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
        Schema::create('status_employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('desc')->nullable();
            $table->boolean('status')->default(true)->comment('Active|inactive');
            $table->morphs('statusable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_employees');
    }
};
