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
            $table->string('lat', 20)->change();
            $table->string('lng', 20)->change();
            $table->decimal('speed', 12, 8)->change();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
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
