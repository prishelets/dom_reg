<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();

            $table->string('mode')->nullable();
            $table->string('type', 50)->nullable();
            $table->unsignedBigInteger('task_id');
            $table->text('text')->nullable();

            // обычный timestamp без updated_at
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
