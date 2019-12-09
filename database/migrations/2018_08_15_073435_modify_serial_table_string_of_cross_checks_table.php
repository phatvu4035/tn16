<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifySerialTableStringOfCrossChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cross_checks', function (Blueprint $table) {
            $table->string('serial')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cross_checks', function (Blueprint $table) {
            $table->bigInteger('serial')->nullable()->change();
        });
    }
}
