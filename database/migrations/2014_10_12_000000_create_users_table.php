<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('mobile')->nullable();
            $table->string('password');
            $table->string('device_key')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('failed_login')->default(0);
            $table->datetime('last_login')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('sudo')->default(false);
            $table->unsignedBigInteger('users_acls_id')->nullable();
            $table->foreign('users_acls_id')->references('id')->on('users_acls');
            $table->rememberToken();
            $table->boolean('status_EI')->default(true);
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
        Schema::dropIfExists('users');
    }
}
