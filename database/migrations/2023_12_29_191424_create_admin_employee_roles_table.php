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
        Schema::create('admin_employee_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_employee_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('status')->default(true)->comment('Active|Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_employee_roles');
    }
};
