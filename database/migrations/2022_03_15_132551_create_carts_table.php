<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('customer', 20)->nullable();
            $table->integer('table')->nullable();
            $table->char('membership_code', 20)->nullable();
            $table->enum('type', ['purchase', 'sales', 'booking', 'other']);
            $table->unsignedBigInteger('sales_category')->nullable();
            $table->decimal('discount', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sub_total', $precision = 18, $scale = 0)->default(0);
            $table->decimal('tax', $precision = 18, $scale = 0)->default(0);
            $table->decimal('total', $precision = 18, $scale = 0)->default(0);
            $table->decimal('pay', $precision = 18, $scale = 0)->default(0);
            $table->decimal('cashBack', $precision = 18, $scale = 0)->default(0);
            $table->string('pMethod', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('status_EI')->default(0);
            $table->unsignedBigInteger('user_created')->nullable();
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
            $table->foreign('membership_code')->references('code')->on('memberships');
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');
            $table->foreign('sales_category')->references('id')->on('sales_categories');

        });
        Schema::create('cart_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity')->default(0);
            $table->decimal('buy_price', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sell_price', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sub_total', $precision = 18, $scale = 0)->default(0);
            $table->text('description')->nullable();
            $table->boolean('served')->default(0);
            $table->timestamps();
            $table->foreign('cart_id')->references('id')->on('carts');
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
        Schema::dropIfExists('cart_details');
        Schema::dropIfExists('carts');
    }
}
