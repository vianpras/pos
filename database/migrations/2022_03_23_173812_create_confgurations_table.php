<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfgurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('total_cart');
            $table->boolean('change_authorization')->default(0);
            $table->boolean('set_inventory')->default(0);
            $table->boolean('set_edit_authorization')->default(0);
            $table->longText('print_footer1')->nullable();
            $table->longText('print_footer2')->nullable();
            $table->longText('print_footer3')->nullable();
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');

        });
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('owner')->nullable();
            $table->string('name')->nullable();
            $table->longText('address1')->nullable();
            $table->longText('address2')->nullable();
            $table->longText('address3')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('website')->nullable();
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configurations');
        Schema::dropIfExists('companies');
        
    }
}
