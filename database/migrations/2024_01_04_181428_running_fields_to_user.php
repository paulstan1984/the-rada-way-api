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
        Schema::table('users', function (Blueprint $table) {
            
            $table->unsignedInteger('runCounter')->default(0);
            $table->decimal('runTotalKm', 18, 2);
            $table->decimal('runningPercentage', 18, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('runCounter');
            $table->dropColumn('runTotalKm');
            $table->dropColumn('runningPercentage');
        });
    }
};
