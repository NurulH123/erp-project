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
        Schema::create('profile_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('status_employee_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('gender', ['perempuan', 'laki-laki']);
            $table->string('phone')->unique();
            $table->text('address');
            $table->string('photo');
            $table->date('join')->nullable();
            $table->date('resaign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_employees');
    }
};
