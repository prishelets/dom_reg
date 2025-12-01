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
    Schema::create('cards', function (Blueprint $table) {
        $table->id();

        $table->string('holder');       // имя владельца на карте
        $table->string('number');       // номер (mask XXX)
        $table->string('exp_month');    // месяц
        $table->string('exp_year');     // год
        $table->string('cvv');          // CVV код
        $table->string('bank')->nullable(); // банк
        $table->boolean('active')->default(true);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
