<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sites', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('name');
            $table->integer('owner')->unsigned(); // ID of one of several tables
            $table->integer('owner_type');
			$table->integer('government');
			$table->foreign('government')->references('id')->on('government_organizations');
            $table->string('slug')->unique();
            $table->boolean('published')->default(false);
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
		Schema::drop('sites');
	}

}
