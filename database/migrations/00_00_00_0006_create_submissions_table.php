<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('empNbr', 6)->nullable();
            $table->string('empRank', 6)->nullable();
            $table->string('inputBy');
            $table->string('formNbr')->unique();
            $table->date('receivedDate');
            $table->integer('quantity');
            $table->string('remark')->nullable();
            $table->string('signed')->nullable();
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
        Schema::dropIfExists('submissions');
    }
}
