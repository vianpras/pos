<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequistionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requistion_details', function (Blueprint $table) {
            $table->id();
            $table->char('requistion_id',20);
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('requistion_id')->references('code')->on('requistions');
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
        Schema::dropIfExists('requistion_details');
    }
}
