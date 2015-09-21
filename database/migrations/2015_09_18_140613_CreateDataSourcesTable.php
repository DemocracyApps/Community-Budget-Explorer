<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataSourcesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('datasources', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('source_type');
			$table->text('description')->nullable();
			$table->integer('organization');
			$table->foreign('organization')->references('id')->on('government_organizations');
			$table->text('properties')->nullable();
			$table->dateTime('last_update')->nullable();
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
		Schema::drop('datasources');
	}

}
