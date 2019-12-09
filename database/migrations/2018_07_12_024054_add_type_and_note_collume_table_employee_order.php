<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeAndNoteCollumeTableEmployeeOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees_order', function (Blueprint $table) {
            $table->string('note');
            $table->string('noi_dung');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees_order', function (Blueprint $table) {
            $table->dropColumn('note');
            $table->dropColumn('noi_dung');

        });
    }
}
