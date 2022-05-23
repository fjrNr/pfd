<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNonAflBoxMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_afl_box_movements', function (Blueprint $table) {
            $table->integer('nonAflBoxId')->unsigned()->primary();
            $table->foreign('nonAflBoxId')->references('id')->on('non_afl_boxes');
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
        Schema::dropIfExists('non_afl_box_movements');
    }
}
