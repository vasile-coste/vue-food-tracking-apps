<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('field_name');
            $table->integer('seed_id');
            $table->string('seed_name');
            $table->integer('seed_company_id');
            $table->string('seed_company_name');
            $table->string('seeding_status');
            $table->integer('fertilizer_id')->nullable();
            $table->string('fertilizer_name')->nullable();
            $table->integer('fertilizer_company_id')->nullable();
            $table->string('fertilizer_company_name')->nullable();
            $table->string('fertilizing_status')->nullable();
            $table->integer('harvesting_company_id')->nullable();
            $table->string('harvesting_company_name')->nullable();
            $table->string('harvesting_status')->nullable();
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
        Schema::dropIfExists('fields');
    }
}
