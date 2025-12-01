<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proxies', function (Blueprint $table) {
            $table->id();

            $table->string('protocol', 10)->default('http'); // http / https / socks5
            
            $table->string('login')->nullable();
            $table->string('password')->nullable();

            $table->string('ip', 100);
            $table->integer('port');

            $table->boolean('active')->default(true);

            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxies');
    }
};
