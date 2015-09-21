<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('socials', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('type', 16); // Network type: facebook, twitter, linkedin, etc.
			$table->integer('userid')->unsigned();
			$table->foreign('userid')->references('id')->on('users');
			$table->bigInteger('socialid')->unsigned();
			$table->string('username');
			$table->string('access_token');
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
		Schema::drop('socials');
	}

}
