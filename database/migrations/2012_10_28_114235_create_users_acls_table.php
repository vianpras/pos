<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersAclsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_acls', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            //menu nav
            $table->string('application',10)->default('');
            $table->string('company',10)->default('');
            $table->string('master',10)->default('');
            $table->string('purchase',10)->default('');
            $table->string('sales',10)->default('');
            $table->string('log_apps',10)->default('');
            $table->string('dashboard',10)->default('');
            $table->string('master_acl',10)->default('');
            $table->string('master_category',10)->default('');
            $table->string('master_item',10)->default('');
            $table->string('master_sales_category',10)->default('');
            $table->string('master_unit',10)->default('');
            $table->string('master_user',10)->default('');
            $table->string('master_docPrefix',10)->default('');
            $table->string('purchase_order',10)->default('');
            $table->string('transaction_purchase',10)->default('');
            $table->string('sales_order',10)->default('');
            $table->string('transaction_sales',10)->default('');
            $table->string('membership',10)->default('');
            $table->string('booking',10)->default('');
            $table->string('purchase_report',10)->default('');
            $table->string('sales_report',10)->default('');
            $table->string('overall_report',10)->default('');
            $table->string('member_profile',10)->default('');
            $table->string('member_booking',10)->default('');
            



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_acls');
    }
}
