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
        Schema::table('tasks', function (Blueprint $table) {
            // Переименовать domain_price → domain_paid_price
            $table->renameColumn('domain_price', 'domain_paid_price');

            // Добавить новую колонку справа
            $table->text('domain_paid_currency')->nullable()->after('domain_paid_price');
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Вернуть всё обратно
            $table->renameColumn('domain_paid_price', 'domain_price');
            $table->dropColumn('domain_paid_currency');
        });
    }
};
