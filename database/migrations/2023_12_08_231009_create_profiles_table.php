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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('gender', ['laki-laki', 'perempuan']);
            $table->string('phone');
            $table->text('address');
            $table->string('photo');
            $table->foreignId('position_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('join');
            $table->date('resaign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
