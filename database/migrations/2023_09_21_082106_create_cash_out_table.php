<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashOutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_out', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_dokumen', 50)->nullable();
            $table->string('akun_kasbank', 50)->nullable();
            $table->date('tgl_transaksi')->nullable();
            $table->double('total_nominal', 8, 2)->nullable();
            $table->string('bayar_kepada', 255)->nullable();
            $table->double('total_pembayaran', 8, 2)->nullable();
            $table->double('total_biaya', 8, 2)->nullable();
            $table->double('balance', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_out');
    }
}
