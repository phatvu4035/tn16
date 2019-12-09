<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserCreatedUserUpdatedOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_info', function (Blueprint $table) {
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);

        });
        Schema::table('roles', function (Blueprint $table) {
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);

        });
        Schema::table('summary', function (Blueprint $table) {
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);

        });
        Schema::table('employee_rent', function (Blueprint $table) {
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('order_info', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
        Schema::table('summary', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });

        Schema::table('employee_rent', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
