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
            $table->string('lat', 50);
            $table->string('lng', 50);
            $table->decimal('speed', 32, 30);
            $table->integer('distance');
            
            $table->unsignedBigInteger('run_id');
            $table->foreign('run_id')->references('id')->on('runs')->onDelete('cascade');
        });

        Schema::table('runs', function (Blueprint $table) {
            //
            $table->dropColumn('locations');
            $table->decimal('distance', 18, 2)->change();
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

        Schema::table('runs', function (Blueprint $table) {
            //
            $table->text('locations');
        });
    }
};
