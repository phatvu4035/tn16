<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMonthYearSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary', function (Blueprint $table) {
           $table->string('month')->nullable();
           $table->string('year')->nullable();

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
            $table->dropColumn('month');
            $table->dropColumn('year');
        });
    }
}
