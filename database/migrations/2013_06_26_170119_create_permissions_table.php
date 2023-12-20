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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('permission_group_id')
                    ->nullable()
                    ->constrained('permissions')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            $table->string('caption');
            $table->boolean('status')->default(true)->comment('active|inactive');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
