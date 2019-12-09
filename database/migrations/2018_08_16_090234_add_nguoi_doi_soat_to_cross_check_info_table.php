<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNguoiDoiSoatToCrossCheckInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cross_check_info', function (Blueprint $table) {
            $table->integer('ke_toan_id')->nullabel()->default(null);
        });

        Schema::table('cross_checks', function (Blueprint $table) {
            $table->integer('tcb_id')->nullabel()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cross_check_info', function (Blueprint $table) {
            $table->dropColumn('ke_toan_id');
        });

        Schema::table('cross_checks', function (Blueprint $table) {
            $table->dropColumn('tcb_id');
        });
    }
}
