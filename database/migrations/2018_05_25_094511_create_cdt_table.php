<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCdtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdt', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable(true);
            $table->integer('level')->nullable(true);
            $table->string('division_name_vn', 255)->nullable(true);
            $table->string('division_name_en', 255)->nullable(true);
            $table->string('complete_code', 255)->nullable(true);
            $table->string('shortened_code', 32)->nullable(true);
            $table->integer('proposal')->unsigned()->nullable(true);
            $table->integer('approved')->unsigned()->nullable(true);
            $table->string('proposal_name', 32)->nullable(true);
            $table->string('approved_name', 32)->nullable(true);
            $table->integer('decision_number')->unsigned()->nullable(true);
            $table->string('upgrade_note', 500)->nullable(true);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('active')->default(0);
            $table->timestamps();
//            $table->foreign('parent_id')->references('id')->on('cdt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cdt');
    }
}
