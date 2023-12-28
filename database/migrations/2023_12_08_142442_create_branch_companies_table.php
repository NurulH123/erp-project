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
        Schema::create('branch_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('address');
            $table->string('phone')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->boolean('status')->default(true)->comment('active|inactive');
            $table->timestamps();
        });
    }

    /**\
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_companies');
    }
};
