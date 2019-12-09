<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hd', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable(true);
            $table->integer('level')->nullable(true);
            $table->string('activity_code', 300)->nullable(true);
            $table->string('body', 300)->nullable(true);
            $table->string('complete_code', 255)->nullable(true);
            $table->string('shortened_code', 32)->nullable(true);
            $table->string('formula', 300)->nullable(true);
            $table->string('define', 300)->nullable(true);
            $table->tinyInteger('tot')->nullable(true);
            $table->tinyInteger('toa')->nullable(true);
            $table->tinyInteger('cf')->nullable(true);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('active')->default(0);
            $table->timestamps();
//            $table->foreign('parent_id')->references('id')->on('hd');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hd');
    }
}
