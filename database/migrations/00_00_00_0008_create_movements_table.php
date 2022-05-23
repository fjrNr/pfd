
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->increments('id');
            $table->date('requestDate');
            $table->date('shippingDate')->nullable();
            $table->string('shippingNo')->unique()->nullable();
            $table->string('shippingNote')->nullable();
            $table->string('newsNo')->unique()->nullable();
            $table->enum('status', config('enums.status'));
            $table->string('formFile')->nullable();
            $table->date('storageDate') ->nullable();
            $table->integer('totalBox');
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
        Schema::dropIfExists('movements');
    }
}
