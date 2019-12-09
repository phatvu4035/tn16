<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalOrderReferenceIdColumnToOrderInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_info', function (Blueprint $table) {
            $table->smallInteger('additional_order')->default(0);
            $table->integer('reference_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_info', function (Blueprint $table) {
            $table->dropColumn('additional_order');
            $table->dropColumn('reference_id');
        });
    }
}
