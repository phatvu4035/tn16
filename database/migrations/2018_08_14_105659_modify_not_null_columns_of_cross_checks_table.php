<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyNotNullColumnsOfCrossChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cross_checks', function (Blueprint $table) {
            $table->string('ma_khach')->nullable()->change();
            $table->string('ten_khach')->nullable()->change();
            $table->bigInteger('ps_co')->nullable()->change();
            $table->string('ma_chung_tu_0')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cross_checks', function (Blueprint $table) {
            $table->string('ma_khach')->nullable(false)->change();
            $table->string('ten_khach')->nullable(false)->change();
            $table->bigInteger('ps_co')->nullable(false)->change();
            $table->string('ma_chung_tu_0')->nullable(false)->change();
        });
    }
}
