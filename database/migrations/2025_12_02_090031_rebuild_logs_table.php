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
        Schema::dropIfExists('logs');

        Schema::create('logs', function (Blueprint $table)
        {
            $table->id();
            
            $table->text('template_name')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            
            $table->text('type')->nullable();
            $table->text('text');
            
            $table->text('error_id');

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
