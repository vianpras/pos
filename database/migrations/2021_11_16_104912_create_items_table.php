<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('code',255)->unique();
            $table->bigInteger('category_id')->unsigned();
            $table->integer('big_quantity')->nullable();
            $table->integer('small_quantity');
            $table->bigInteger('big_unit_id')->unsigned()->nullable();
            $table->bigInteger('small_unit_id')->unsigned();
            $table->decimal('buy_price',$precision = 18, $scale = 0)->default(0);
            $table->decimal('sell_price',$precision = 18, $scale = 0)->default(0);
            $table->decimal('profit',$precision = 18, $scale = 0)->default(0);
            $table->boolean('status')->default(1);
            $table->boolean('status_EI')->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('big_unit_id')->references('id')->on('units');
            $table->foreign('small_unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
