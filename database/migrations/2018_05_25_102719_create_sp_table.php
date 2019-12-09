<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable(true);
            $table->integer('level')->unsigned()->nullable(true);
            $table->string('product_name_vn', 255)->nullable(true);
            $table->string('product_name_en', 255)->nullable(true);
            $table->string('complete_code', 255)->nullable(true);
            $table->string('shortened_code', 32)->nullable(true);
            $table->tinyInteger('payment_outside')->nullable(true);
            $table->tinyInteger('payment_inside')->nullable(true);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('active')->default(0);
            $table->timestamps();
//            $table->foreign('parent_id')->references('id')->on('sp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sp');
    }
}
