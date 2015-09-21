<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('site');
            $table->foreign('site')->references('id')->on('sites');
            $table->string('title');
			$table->string('short_name'); // for URLs
			$table->string('menu_name'); // to display in menus
            $table->string('description')->nullable();
            $table->integer('ordinal')->nullable();
            $table->boolean('show_in_menu')->default(true);
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
		Schema::drop('pages');
	}

}
