<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_categories', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('name');
            $table->integer('chart');
            $table->foreign('chart')->references('id')->on('account_charts');
            $table->text('description')->nullable();
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
		Schema::drop('account_categories');
	}

}
