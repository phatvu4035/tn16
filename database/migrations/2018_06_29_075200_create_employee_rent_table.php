<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeRentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_rent', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('identity_code')->unsigned()->unique();
            $table->string('identity_type')->default('cmt');
            $table->dateTime('emp_code_date')->nullable();
            $table->string('emp_code_place')->nullable();
            $table->string('emp_name');
            $table->string('emp_tax_code')->nullable();
            $table->string('emp_country')->nullable();
            $table->tinyInteger('emp_live_status');
            $table->string('emp_account_number')->nullable();
            $table->string('emp_account_bank')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('employee_rent');
    }
}
