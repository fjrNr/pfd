<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submission_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aflNbr', 9)->unique();
            $table->integer('submissionId')->unsigned();
            $table->foreign('submissionId')->references('id')->on('submissions');
            $table->boolean('flightPlan')->default(false);
            $table->boolean('dispatchRelease')->default(false);
            $table->boolean('weatherForecast')->default(false);
            $table->boolean('notam')->default(false);
            $table->boolean('toLdgDataCard')->default(false);
            $table->boolean('loadSheet')->default(false);
            $table->boolean('fuelReceipt')->default(false);
            $table->boolean('paxManifest')->default(false);
            $table->boolean('notoc')->default(false);
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
        Schema::dropIfExists('submission_details');
    }
}
