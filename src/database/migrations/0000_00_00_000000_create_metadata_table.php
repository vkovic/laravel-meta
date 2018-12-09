<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel-meta.table_name'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('realm', 128);
            $table->string('metable_type', 128)->default('');
            $table->string('metable_id', 128)->default('');
            $table->string('key', 128);
            $table->longText('value');

            $table->timestamps();

            $table->unique(['realm', 'metable_type', 'metable_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('laravel-meta.table_name'));
    }
}