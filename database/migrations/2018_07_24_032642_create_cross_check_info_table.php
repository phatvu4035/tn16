<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrossCheckInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cross_check_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phap_nhan');
            $table->integer('thang');
            $table->integer('nam');
            $table->integer('ke_toan_check')->default(0);//0 : chưa thông qua kế toán đối soát | 1: đã thông qua kế toán đối soát
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
        Schema::dropIfExists('cross_check_infos');
    }
}
