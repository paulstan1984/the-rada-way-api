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
        Schema::table('locations', function (Blueprint $table) {
            //
            $table->string('lat', 10)->change();
            $table->string('lng', 10)->change();
            $table->decimal('speed', 12, 8)->change();
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
        Schema::table('locations', function (Blueprint $table) {
            
            $table->string('lat', 50)->change();
            $table->string('lng', 50)->change();
            $table->decimal('speed', 32, 30)->change();
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};
