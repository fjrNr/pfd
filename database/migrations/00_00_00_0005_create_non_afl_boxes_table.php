<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNonAflBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_afl_boxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('packNbr')->unique();
            $table->tinyinteger('boxNbr')->nullable();
            $table->enum('location', config('enums.location'));
            $table->date('classOfDate')->nullable();
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
        Schema::dropIfExists('non_afl_boxes');
    }
}
