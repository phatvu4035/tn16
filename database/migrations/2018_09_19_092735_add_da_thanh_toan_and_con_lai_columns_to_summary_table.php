<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDaThanhToanAndConLaiColumnsToSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary', function (Blueprint $table) {
            $table->bigInteger('da_thanh_toan')->default(0);
            $table->bigInteger('con_lai_can_thanh_toan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('summary', function (Blueprint $table) {
            $table->dropColumn('da_thanh_toan');
            $table->dropColumn('con_lai_can_thanh_toan');
        });
    }
}
