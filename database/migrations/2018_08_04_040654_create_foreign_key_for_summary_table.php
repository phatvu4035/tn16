<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKeyForSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summary', function (Blueprint $table) {
            $table->dropForeign('summary_order_id_foreign');

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
        Schema::table('summary', function (Blueprint $table) {
            $table->dropForeign('summary_order_id_foreign');
        });
    }
}
