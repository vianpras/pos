<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->char('code',20)->unique();
            $table->string('nama');
            $table->string('mobile')->unique();
            $table->string('kota')->default('');
            $table->string('provinsi')->default('');
            $table->string('negara')->default('indonesia');
            $table->text('address')->nullable();
            $table->string('email')->default('');
            $table->enum('status',['active','suspend','close']);
            $table->boolean('status_EI')->default(0);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');
        });
        Schema::create('purchases', function (Blueprint $table) {
            $table->char('code', 20)->unique();
            $table->char('supplier_code', 20)->nullable();
            $table->string('supplier');
            $table->date('date_order');
            $table->enum('status', ['pending', 'confirm', 'cancel', 'close']);
            $table->decimal('discount', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sub_total', $precision = 18, $scale = 0)->default(0);
            $table->decimal('tax', $precision = 18, $scale = 0)->default(0);
            $table->decimal('total', $precision = 18, $scale = 0)->default(0);
            $table->text('description')->nullable();
            $table->boolean('status_EI')->default(0);
            $table->unsignedBigInteger('user_created')->nullable();
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->foreign('supplier_code')->references('code')->on('suppliers');
            $table->timestamps();
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');
        });
        Schema::create('purchases_details', function (Blueprint $table) {
            $table->id();
            $table->char('purchases_id', 20);
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity')->default(0);
            $table->decimal('buy_price', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sell_price', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sub_total', $precision = 18, $scale = 0)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('purchases_id')->references('code')->on('purchases');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases_details');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
    }
}
