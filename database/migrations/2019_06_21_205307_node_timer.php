<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NodeTimer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('node', function(Blueprint $table)
        {
            $table->string('timer')->nullable();
            $table->string('timer_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('node', function(Blueprint $table)
        {
            $table->dropColumn('timer');
            $table->dropColumn('timer_type');
        });
    }
}
