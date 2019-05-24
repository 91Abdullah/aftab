<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_numbers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number');
            $table->boolean('status')->default(false);
            $table->unsignedBigInteger('upload_list_id');

            $table->foreign('upload_list_id')->references('id')->on('upload_lists')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('list_numbers');
    }
}
