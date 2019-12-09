<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeyForEmployessOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("employees_order", function (Blueprint $table) {
            $table->dropForeign('employees_order_order_id_foreign');

            $table->foreign('order_id')
                ->references('id')->on('order_info')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("employees_order", function (Blueprint $table) {
            //
        });
    }
}
