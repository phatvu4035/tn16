<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees_order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_code');
            $table->string('employee_table');
            $table->string('phap_nhan');
            $table->string('san_pham');
            $table->float('salary_base', 12)->nullable();
            $table->float('salary_action', 12)->nullable();
            $table->float('salary_working_day', 12)->nullable();
            $table->float('salary_working_ot_time', 12)->nullable();
            $table->float('salary_sub', 12)->nullable();
            $table->float('salary_other', 12)->nullable();
            $table->float('com', 12)->nullable();
            $table->float('bonus', 12)->nullable();
            $table->float('rent', 12)->nullable();
            $table->float('interest', 12)->nullable();
            $table->float('bao_hiem', 12)->nullable();
            $table->float('giam_tru_ban_than', 12)->nullable();
            $table->float('total_salary_sub_other', 12)->nullable();
            $table->float('giam_tru_nguoi_phu_thuoc', 12)->nullable();
            $table->float('thu_nhap_khong_chiu_thue', 12)->nullable();
            $table->float('tong_giam_tru_tinh_thue', 12)->nullable();
            $table->float('thu_nhap_tinh_thue', 12)->nullable();
            $table->float('tncn', 12)->nullable();
            $table->float('thuc_nhan', 12)->nullable();
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
        Schema::dropIfExists('employees_order');
    }
}
