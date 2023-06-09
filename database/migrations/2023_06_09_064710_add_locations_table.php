<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->decimal('lat', 47);
            $table->decimal('lng', 47);
            $table->decimal('speed', 47);
            $table->integer('distance');
            
            $table->unsignedBigInteger('run_id');
            $table->foreign('run_id')->references('id')->on('runs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
};
