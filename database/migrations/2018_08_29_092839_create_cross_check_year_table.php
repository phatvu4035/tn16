<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrossCheckYearTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cross_check_year', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial')->nullable();
            $table->dateTime('ngay_chung_tu');
            $table->string('ma_chung_tu');
            $table->string('so_chung_tu');
            $table->string('ma_khach')->nullable();
            $table->string('ten_khach')->nullable();
            $table->text('dien_giai');
            $table->bigInteger('tai_khoan')->nullable();
            $table->bigInteger('tai_khoan_doi_ung');
            $table->bigInteger('ps_no');
            $table->bigInteger('ps_co');
            $table->bigInteger('thue');
            $table->string('ma_du_an')->nullable();
            $table->string('ma_chung_tu_0');
            $table->string('phap_nhan');
            $table->string('status');
            $table->integer('info_id');
            $table->integer('order_id')->nullable();
            $table->integer('tcb_id')->nullable();
            $table->integer('active')->default(1);
            $table->string('reason');
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
        Schema::dropIfExists('cross_check_year');
    }
}
