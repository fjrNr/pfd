<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAflCopiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afl_copies', function (Blueprint $table) {
            $table->increments('id');
            $table->char('aflnbr',8);
            $table->date('depdate');
            $table->date('arrdate');
            $table->char('depstn',3);
            $table->char('arrstn',3);
            $table->string('fltnbr',4);
            $table->char('picnew',6);
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
        Schema::dropIfExists('afl_copies');
    }
}
