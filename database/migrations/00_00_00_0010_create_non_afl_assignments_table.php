<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNonAflAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_afl_assignments', function (Blueprint $table) {
            $table->integer('submissionId')->unsigned()->primary();
            $table->foreign('submissionId')->references('id')->on('submissions');
            $table->integer('nonAflBoxId')->unsigned();
            $table->foreign('nonAflBoxId')->references('id')->on('non_afl_boxes');
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
        Schema::dropIfExists('non_afl_assignments');
    }
}
