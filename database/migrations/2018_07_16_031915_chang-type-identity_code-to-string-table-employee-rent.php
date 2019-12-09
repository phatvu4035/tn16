<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangTypeIdentityCodeToStringTableEmployeeRent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_rent', function (Blueprint $table) {

            $table->string('identity_code')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_rent', function (Blueprint $table) {
            $table->integer('identity_code')->unsigned()->unique()->change();

        });
    }
}
