<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequistionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requistions', function (Blueprint $table) {
            $table->char('code',20)->unique();
            $table->date('date_request');
            $table->date('date_need');
            $table->string('project_id',20)->nullable();
            $table->enum('status',['clear','draft','cancel','close']);
            $table->text('description')->nullable();
            $table->boolean('status_EI')->default(0);
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();
            $table->foreign('project_id')->references('code')->on('projects');
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
        Schema::dropIfExists('requistions');
    }
}
