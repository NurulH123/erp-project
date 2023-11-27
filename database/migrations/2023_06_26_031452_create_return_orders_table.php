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
        Schema::create('return_orders', function (Blueprint $table) {
            $table->id();
            $table->string('return_id');
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->string('order_code');
            $table->timestamp('order_date');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->bigInteger('quantity');
            $table->enum('return_reason',['Dead On Arrival','Faulty, please supply details','Order Error','Other, please supply details','Received Wrong Item']);
            $table->enum('opened',['opened','Unopened']);
            $table->string('comment')->nullable();
            $table->enum('return_action',['Credit Issued','Refund','Replacement Sent']);
            $table->enum('status',['Awaiting Products','Complete','Pending']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_orders');
    }
};
