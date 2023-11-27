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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->unsignedBigInteger('address_id');
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons');
            $table->bigInteger('sub_total');
            $table->bigInteger('ongkos_kirim');
            $table->bigInteger('potongan_ongkir')->nullable();
            $table->bigInteger('final_ongkir');
            $table->bigInteger('total_price');
            $table->bigInteger('upgrade_price')->nullable();
            $table->unsignedBigInteger('payment_id');
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->unsignedBigInteger('sending_id');
            $table->foreign('sending_id')->references('id')->on('sendings');
            $table->text('description')->nullable();
            $table->text('note')->nullable();
            $table->enum('status',
            [
                'Order (Foto Belum Dikirim)',
                'Order (Foto Sudah Dikirim)',
                'Layout',
                'Print Quality Control',
                'Finishing',
                'Packing',
                'Kirim',
                'Komplain (Baru)',
                'Komplain (Selesai)',
            ]);
            $table->string('nama_rekening');
            $table->string('special')->default('false');
            $table->string('nomor_resi')->nullable();
            $table->string('sumber_lead')->nullable();
            $table->string('jenis_lead')->nullable();
            $table->integer('kode');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
