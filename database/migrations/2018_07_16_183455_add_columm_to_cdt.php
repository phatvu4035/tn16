<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColummToCdt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cdt', function (Blueprint $table) {
            $table->integer('user_create_id')->unsigned()->nullable(true)->after('approved_name');
            $table->string('user_create_name', 32)->nullable(true)->after('user_create_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cdt', function (Blueprint $table) {
            $table->dropColumn('user_create_id');
            $table->dropColumn('user_create_name');
        });
    }
}
