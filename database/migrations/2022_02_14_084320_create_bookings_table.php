<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->char('code', 20)->unique();
            $table->string('nik')->nullable();
            $table->string('name');
            $table->string('mobile');
            $table->string('address')->nullable();
            $table->string('necessary');
            $table->date('date_booking');
            $table->decimal('discount', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sub_total', $precision = 18, $scale = 0)->default(0);
            $table->decimal('tax', $precision = 18, $scale = 0)->default(0);
            $table->decimal('total', $precision = 18, $scale = 0)->default(0);

            $table->enum('status', ['pending', 'confirm', 'cancel', 'close']);
            $table->boolean('status_EI')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('user_created')->nullable();
            $table->char('membership_code', 20)->nullable();
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->foreign('membership_code')->references('code')->on('memberships');
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');
        });
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->char('booking_id', 20);
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity')->default(0);
            $table->decimal('buy_price', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sell_price', $precision = 18, $scale = 0)->default(0);
            $table->decimal('sub_total', $precision = 18, $scale = 0)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('booking_id')->references('code')->on('bookings');
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
        Schema::dropIfExists('booking_details');
        Schema::dropIfExists('bookings');
    }
}
