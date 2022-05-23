<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAflBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_boxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('packNbr')->unique();
            $table->enum('location', config('enums.location'));
            $table->string('notes');
            $table->date('endRetentionDate');
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
        Schema::dropIfExists('afl_boxes');
    }
}
