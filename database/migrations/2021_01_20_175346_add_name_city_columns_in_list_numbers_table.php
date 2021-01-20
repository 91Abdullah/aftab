<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNameCityColumnsInListNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_numbers', function (Blueprint $table) {
            $table->string('name')->default('')->after('number');
            $table->string('city')->default('')->after('number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('list_numbers', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('city');
        });
    }
}
