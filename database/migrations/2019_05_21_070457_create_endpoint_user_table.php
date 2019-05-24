<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEndpointUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('endpoint_user', function (Blueprint $table) {
            $table->string('ps_endpoint_id', 40)->collation('latin1_swedish_ci');
            $table->unsignedBigInteger('user_id');
            $table->primary(['ps_endpoint_id', 'user_id']);

            //DB::statement("ALTER TABLE endpoint_user CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci");

            $table->foreign('ps_endpoint_id')->references('id')->on('ps_endpoints')
                ->onDelete('cascade')
                ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('endpoint_user', function (Blueprint $table) {
            $table->dropIfExists('endpoint_user');
        });
    }
}
