<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chart_of_account_id')->nullable();
            $table->string('code_account_default', 255);
            $table->string('code_parent', 255)->nullable();
            $table->string('code_account_alias', 255)->nullable();
            $table->tinyInteger('is_coa_alias')->default(0);
            $table->string('name', 255);
            $table->enum('group_of_account', ['aktiva','hutang','modal','pendapatan','harga_pokok_penjualan','biaya_operasional','biaya_dan_pendapatan_lainnya']);
            $table->enum('type_of_account', ['header','detail']);
            $table->enum('type_of_business', ['dagang','jasa','manufaktur','proyek']);
            $table->text('description');
            $table->bigInteger('user_created');
            $table->bigInteger('user_updated')->nullable();
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
        Schema::dropIfExists('chart_of_accounts');
    }
}
