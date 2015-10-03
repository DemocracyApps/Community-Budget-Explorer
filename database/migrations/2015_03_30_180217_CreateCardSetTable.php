<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardSetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('card_sets', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('name');
            $table->integer('site');
            $table->foreign('site')->references('id')->on('sites');
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
		Schema::drop('card_sets');
	}

}
