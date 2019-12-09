<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RolePhapNhanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_phap_nhan', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('role_id')->unsigned()->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('set null');

            $table->integer('phap_nhan_id')->unsigned()->nullable();
            $table->foreign('phap_nhan_id')->references('id')->on('permissions')->onUpdate('cascade')->onDelete('set null');

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
        Schema::dropIfExists('role_phap_nhan');
    }
}
