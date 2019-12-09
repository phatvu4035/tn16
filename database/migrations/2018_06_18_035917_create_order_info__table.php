<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// ma osscar
// ma DT
// ngay de xuat
// ma phong ban (ko required)
// nguoi de xuat
// noi dung
// nguoi huong (ko required)
// so tien
// loai tien (VND, USD...)
// tỉ giá
// số seri (ko required)

class CreateOrderInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ma_osscar')->nullable();
            $table->string('ma_du_toan')->nullable();
            $table->string('serial')->nullable();
            $table->string('phap_nhan')->nullable();
            $table->string('nguoi_de_xuat')->nullable();
            $table->string('phong_ban')->nullable();
            $table->dateTime('ngay_de_xuat')->nullable();
            $table->string('nguoi_huong')->nullable();
            $table->string('noi_dung')->nullable();
            $table->double('so_tien')->nullable();
            $table->string('loai_tien')->nullable();
            $table->double('ty_gia')->nullable();
            $table->double('quy_doi')->nullable();
 			$table->string('month')->nullable();
            $table->string('year')->nullable();
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
        Schema::dropIfExists('order_info');
    }
}
