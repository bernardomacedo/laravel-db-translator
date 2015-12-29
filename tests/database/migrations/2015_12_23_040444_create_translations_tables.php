<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Create the variables table
         */
        if (!Schema::hasTable('translations_variables')) {
            Schema::create('translations_variables', function (Blueprint $table) {
                $table->increments('id');
                $table->text('text');
                $table->text('group');
                $table->string('md5sha1', 255);
                $table->boolean('dynamic')->default(0);
                $table->timestamps();
            });
        }
        /**
         * Create the translated table
         */
        if (!Schema::hasTable('translations_translated')) {
            Schema::create('translations_translated', function (Blueprint $table) {
                $table->integer('variable_id')->unsigned();
                $table->integer('language_id')->unsigned();
                $table->text('translation');
                $table->timestamps();
                $table->foreign('variable_id')->references('id')->on('translations_variables')->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade')->onDelete('cascade');
                $table->primary(['variable_id', 'language_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translations_translated');
        Schema::drop('translations_variables');
    }
}
