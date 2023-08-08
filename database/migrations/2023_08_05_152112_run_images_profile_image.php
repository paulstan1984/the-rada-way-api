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
        Schema::table('runs', function (Blueprint $table) {
            $table->longText('base64_encoded_images')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->longText('base64_encoded_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('runs', function (Blueprint $table) {
            $table->dropColumn('base64_encoded_images');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('base64_encoded_image');
        });
    }
};
