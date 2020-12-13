<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identifier');
            $table->string('token');
            $table->integer('validity');
            $table->boolean('expired')->default(false);
            $table->integer('no_times_generated')->default(0);
            $table->integer('no_times_attempted')->default(0);
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }
}
