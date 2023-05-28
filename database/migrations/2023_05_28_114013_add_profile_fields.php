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
            $table->string('sex', 50)->default('N');
            $table->date('dob')->nullable();
            $table->unsignedInteger('height')->default(170);
            $table->unsignedInteger('weight')->default(75);
            $table->unsignedInteger('runGoal')->default(100);
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
            $table->dropColumn('sex');
            $table->dropColumn('dob');
            $table->dropColumn('height');
            $table->dropColumn('weight');
            $table->dropColumn('runGoal');
        });
    }
};
