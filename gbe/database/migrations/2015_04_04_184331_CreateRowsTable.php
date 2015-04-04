<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRowsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rows', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('page_id');
            $table->foreign('page_id')->references('id')->on('pages');
            $table->string('title')->nullable();
            $table->integer('layout')->nullable();
            $table->foreign('layout')->references('id')->on("layouts");
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
		Schema::drop('rows');
	}

}
