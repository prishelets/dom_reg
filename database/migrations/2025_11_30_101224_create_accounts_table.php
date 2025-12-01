<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;





return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table)
        {
            $table->id();

            $table->string('email', 255)->nullable();
            $table->string('login', 255)->nullable();
            $table->string('password', 255)->nullable();

            $table->string('email_login', 255)->nullable();
            $table->string('email_password', 255)->nullable();

            $table->string('proxy', 255)->nullable();

            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('zip', 50)->nullable();
            $table->string('phone', 100)->nullable();

            $table->text('security_qa')->nullable();

            $table->string('status', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
