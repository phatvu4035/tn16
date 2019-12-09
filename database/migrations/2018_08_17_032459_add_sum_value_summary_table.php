<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSumValueSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary', function (Blueprint $table) {
            $table->bigInteger('sum_thu_nhap_truoc_thue')->default(0);
            $table->bigInteger('sum_non_tax')->default(0);
            $table->bigInteger('sum_tnct')->default(0);
            $table->bigInteger('sum_bhxh')->default(0);
            $table->bigInteger('sum_thue_tam_trich')->default(0);
            $table->bigInteger('sum_thuc_nhan')->default(0);
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
            $table->dropColumn('sum_thu_nhap_truoc_thue');
            $table->dropColumn('sum_non_tax');
            $table->dropColumn('sum_tnct');
            $table->dropColumn('sum_bhxh');
            $table->dropColumn('sum_thue_tam_trich');
            $table->dropColumn('sum_thuc_nhan');
        });
    }
}
