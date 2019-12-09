<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_code');
            $table->string('employee_table');
            $table->string('phap_nhan');
            $table->string('san_pham');
            $table->string('ma_so_thue')->nullable();

            $table->bigInteger('tong_thu_nhap_truoc_thue')->default(0);
            $table->bigInteger('tong_non_tax')->default(0);
            $table->bigInteger('tong_tnct')->default(0);
            $table->bigInteger('bhxh')->default(0);
            $table->bigInteger('thue_tam_trich')->default(0);
            $table->bigInteger('thuc_nhan')->default(0);
            $table->bigInteger('giam_tru_ban_than')->default(0);
            $table->bigInteger('giam_tru_gia_canh')->default(0);
            $table->integer('type')->unsigned();
            $table->foreign('type')->references('id')->on('type');

            $table->string('note');
            $table->string('ref');
            $table->text('noi_dung');

            $table->tinyInteger('status')->default(0);

            $table->string('vi_tri')->nullable();
            $table->string('cdt')->nullable();
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('order_info');
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
        Schema::dropIfExists('summary');
    }
}
