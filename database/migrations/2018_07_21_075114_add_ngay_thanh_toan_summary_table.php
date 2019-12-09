<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNgayThanhToanSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary', function (Blueprint $table) {
            $table->dateTime('ngay_thanh_toan')->nullable();

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
            $table->dropColumn('ngay_thanh_toan');
        });
    }
}
