<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountChartsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('account_charts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('government_organization');
			$table->foreign('government_organization')->references('id')->on('government_organizations');
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
		Schema::drop('account_charts');
	}

}
