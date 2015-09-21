<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cards', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('card_set');
            $table->integer('site');
            $table->foreign('site')->references('id')->on('sites');
            $table->integer('ordinal')->unsigned()->nullable();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->text('properties')->nullable();
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
		Schema::drop('cards');
	}

}
