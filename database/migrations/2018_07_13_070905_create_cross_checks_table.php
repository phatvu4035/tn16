<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrossChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cross_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('serial')->nullable();
            $table->dateTime('ngay_chung_tu');
            $table->string('ma_chung_tu');
            $table->string('so_chung_tu');
            $table->string('ma_khach');
            $table->string('ten_khach');
            $table->text('dien_giai');
            $table->bigInteger('tai_khoan');
            $table->bigInteger('tai_khoan_doi_ung');
            $table->bigInteger('ps_no');
            $table->bigInteger('ps_co');
            $table->string('ma_du_an')->nullable();
            $table->string('ma_chung_tu_0');
            $table->string('status');
            $table->integer('order_id')->nullable();
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
        Schema::dropIfExists('cross_checks');
    }
}
