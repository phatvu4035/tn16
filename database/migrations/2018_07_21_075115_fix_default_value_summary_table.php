<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixDefaultValueSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary', function (Blueprint $table) {
            $table->bigInteger('tong_thu_nhap_truoc_thue')->nullable()->default(0)->change();
            $table->bigInteger('tong_non_tax')->nullable()->default(0)->change();
            $table->bigInteger('tong_tnct')->nullable()->default(0)->change();
            $table->bigInteger('bhxh')->nullable()->default(0)->change();
            $table->bigInteger('thue_tam_trich')->nullable()->default(0)->change();
            $table->bigInteger('thuc_nhan')->nullable()->default(0)->change();
            $table->bigInteger('giam_tru_ban_than')->nullable()->default(0)->change();
            $table->bigInteger('giam_tru_gia_canh')->nullable()->default(0)->change();

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
            $table->bigInteger('tong_thu_nhap_truoc_thue')->default(0)->change();
            $table->bigInteger('tong_non_tax')->default(0)->change();
            $table->bigInteger('tong_tnct')->default(0)->change();
            $table->bigInteger('bhxh')->default(0)->change();
            $table->bigInteger('thue_tam_trich')->default(0)->change();
            $table->bigInteger('thuc_nhan')->default(0)->change();
            $table->bigInteger('giam_tru_ban_than')->default(0)->change();
            $table->bigInteger('giam_tru_gia_canh')->default(0)->change();
        });
    }
}
