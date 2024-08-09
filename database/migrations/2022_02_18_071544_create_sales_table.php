<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->decimal('mark_up', $precision = 18, $scale = 0)->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_created')->nullable();
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');
            $table->boolean('status')->default(1);
            $table->boolean('status_EI')->default(0);
            $table->timestamps();
        });
        Schema::create('sales', function (Blueprint $table) {
            $table->char('code', 20)->unique();
            $table->string('customer', 20)->nullable();
            $table->integer('table')->nullable();
            $table->char('membership_code', 20)->nullable();
            $table->date('date_order');
            $table->enum('status', ['pending', 'confirm', 'cancel', 'close']);
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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id();
            $table->char('sales_id', 20);
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity')->default(0);
            $table->decimal('buy_price', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sell_price', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sub_total', $precision = 18, $scale = 0)->default(0);
            $table->text('description')->nullable();
            $table->boolean('served')->default(0);
            $table->timestamps();
            $table->foreign('sales_id')->references('code')->on('sales');
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
        Schema::dropIfExists('sales_details');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('sales_categories');

    }
}
