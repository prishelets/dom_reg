<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::table('cards', function (Blueprint $table) {

            // bank -> label
            if (Schema::hasColumn('cards', 'bank')) {
                $table->renameColumn('bank', 'label');
            }

            // удаляем active
            if (Schema::hasColumn('cards', 'active')) {
                $table->dropColumn('active');
            }

            // дополнительные поля
            $table->timestamp('card_last_used_at')->nullable()->after('label');
            $table->timestamp('card_next_used_at')->nullable()->after('card_last_used_at');

            $table->integer('success_count')->default(0)->after('card_next_used_at');
            $table->integer('error_count')->default(0)->after('success_count');
        });
    }

    public function down()
    {
        Schema::table('cards', function (Blueprint $table) {

            // возвращаем active обратно
            $table->boolean('active')->default(true);

            // возвращаем label -> bank
            if (Schema::hasColumn('cards', 'label')) {
                $table->renameColumn('label', 'bank');
            }

            $table->dropColumn([
                'card_last_used_at',
                'card_next_used_at',
                'success_count',
                'error_count'
            ]);
        });
    }
};
