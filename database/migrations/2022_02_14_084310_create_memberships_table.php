<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->char('code',20)->unique();
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('mobile')->unique();
            $table->enum('gender',['l','p','o']);
            $table->string('kota')->default('');
            $table->string('provinsi')->default('');
            $table->string('negara')->default('indonesia');
            $table->text('address')->nullable();
            $table->string('email')->default('');
            $table->string('place_birth');
            $table->date('date_birth');
            $table->date('expired');
            $table->decimal('ballance', $precision = 18, $scale = 0)->default(0);
            $table->decimal('point', $precision = 18, $scale = 0)->default(0);
            $table->enum('status',['active','suspend','close']);
            $table->boolean('status_EI')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('member_logins_id');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->foreign('member_logins_id')->references('id')->on('member_logins');
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
        Schema::dropIfExists('memberships');
    }
}
