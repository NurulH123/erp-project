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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->bigInteger('discount');
            $table->enum('category',['Price','Ongkos Kirim']);
            $table->enum('type',['Percentage','Fixed Amount']);
            $table->timestamp('date_start');
            $table->timestamp('date_end');
            $table->integer('coupon_uses');
            $table->integer('customer_uses');
            $table->enum('status',['enabled','disabled'])->default('enabled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
