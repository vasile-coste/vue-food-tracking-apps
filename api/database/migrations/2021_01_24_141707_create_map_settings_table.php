<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->boolean('show_joystick')->default(false);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('precision')->default(3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('map_settings');
    }
}
