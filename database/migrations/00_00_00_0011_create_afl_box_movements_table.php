<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAflBoxMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_box_movements', function (Blueprint $table) {
            $table->integer('aflBoxId')->unsigned()->primary();
            $table->foreign('aflBoxId')->references('id')->on('afl_boxes');
            $table->integer('movementId')->unsigned();
            $table->foreign('movementId')->references('id')->on('movements');
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
        Schema::dropIfExists('afl_box_movements');
    }
}
