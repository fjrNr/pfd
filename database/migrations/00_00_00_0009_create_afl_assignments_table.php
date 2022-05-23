<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAflAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_assignments', function (Blueprint $table) {
            $table->integer('aflCopyId')->unsigned()->primary();
            $table->foreign('aflCopyId')->references('id')->on('afl_copies');
            $table->integer('aflBoxId')->unsigned();
            $table->foreign('aflBoxId')->references('id')->on('afl_boxes');
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
        Schema::dropIfExists('afl_assignments');
    }
}
