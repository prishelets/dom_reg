<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('proxies', function (Blueprint $table) {
            $table->string('mode')->nullable()->after('label');
            $table->text('info')->nullable()->after('mode'); 
        });
    }

    public function down()
    {
        Schema::table('proxies', function (Blueprint $table) {
            $table->dropColumn('mode');
            $table->dropColumn('info');
        });
    }
};
