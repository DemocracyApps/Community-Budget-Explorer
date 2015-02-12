<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserConfirmationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_confirmations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user');
			$table->foreign('user')->references('id')->on('users');
			$table->string('type');
			$table->string('code');
			$table->dateTime('expires');
			$table->boolean('done')->default(false);
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
		Schema::drop('user_confirmations');
	}

}
