<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosBukuBesarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_buku_besar', function (Blueprint $table) {
            $table->id();
            $table->string('no_buku_besar', 50);
            $table->string('no_jurnal_umum', 50);
            $table->date('tgl_transaksi')->nullable();
            $table->string('no_transaksi', 100)->nullable();
            $table->string('tipe', 50)->nullable();
            $table->string('kode_akun', 50)->nullable();
            $table->double('debit', 8, 2)->nullable();
            $table->double('kredit', 8, 2)->nullable();
            $table->string('sts_buku_besar', 1)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('sts_doc', 1)->nullable();
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
        Schema::dropIfExists('pos_buku_besar');
    }
}
