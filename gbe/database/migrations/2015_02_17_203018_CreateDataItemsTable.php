<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_items', function(Blueprint $table)
		{
			$table->increments('id');
            $table->decimal('amount', 12, 2);
            $table->integer('account');
            $table->integer('category1')->nullable();
            $table->integer('category2')->nullable();
            $table->integer('category3')->nullable();
            $table->text('categoryN')->nullable();
            $table->integer('dataset');
            $table->foreign('dataset')->references('id')->on('datasets');
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
		Schema::drop('data_items');
	}

}
