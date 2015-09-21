<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageComponentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('page_components', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('component');
            $table->foreign('component')->references('id')->on('components');
            $table->integer('page');
            $table->foreign('page')->references('id')->on('pages');
            $table->string('target')->nullable();
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
		Schema::drop('page_components');
	}

}
