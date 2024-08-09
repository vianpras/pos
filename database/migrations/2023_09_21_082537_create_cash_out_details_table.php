<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashOutDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_out_details', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cash_out');
            $table->string('akun_biaya', 50)->nullable();
            $table->date('tgl_pelaksanaan')->nullable();
            $table->double('nominal', 8, 2)->nullable();
            $table->string('keperluan', 255)->nullable();
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
        Schema::dropIfExists('cash_out_details');
    }
}
