<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pt', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable(true);
            $table->integer('level')->nullable(true);
            $table->string('name_native', 255)->nullable(true);
            $table->string('short_name', 255)->nullable(true);
            $table->string('name_en', 255)->nullable(true);
            $table->string('complete_code', 255)->nullable(true);
            $table->string('short_code', 32)->nullable(true);
            $table->string('tax_code', 32)->nullable(true);
            $table->string('location', 32)->nullable(true);
            $table->string('address_in_country', 300)->nullable(true);
            $table->string('address_in_english', 300)->nullable(true);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('active')->default(0);
            $table->timestamps();
//            $table->foreign('parent_id')->references('id')->on('pt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pt');
    }
}
