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
        Schema::create('tasks', function (Blueprint $table) {

            $table->id();

            $table->string('registrar', 30);
            $table->string('domain', 255);
            $table->string('country', 100)->nullable();
            $table->string('brand', 255)->nullable();

            $table->boolean('completed')->default(0);
            $table->string('status', 255)->nullable();

            $table->string('registrar_email', 255)->nullable();
            $table->string('registrar_login', 255)->nullable();

            $table->string('email_login', 255)->nullable();
            $table->string('email_password', 255)->nullable();

            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('zip', 50)->nullable();
            $table->string('phone', 100)->nullable();

            $table->string('proxy', 255)->nullable();

            $table->text('security_qa')->nullable();

            $table->boolean('domain_paid')->default(0);
            $table->dateTime('domain_paid_date')->nullable();
            $table->decimal('domain_price', 10, 2)->nullable();

            $table->string('cloudflare_email', 255)->nullable();
            $table->string('cloudflare_password', 255)->nullable();
            $table->text('api_key_global')->nullable();
            $table->text('api_key_custom')->nullable();

            $table->text('ns_servers')->nullable();

            $table->boolean('ns_at_registrar')->default(0);
            $table->dateTime('ns_last_check_at')->nullable();

            $table->timestamps(); // created_at, updated_at

            $table->dateTime('account_created_at')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
