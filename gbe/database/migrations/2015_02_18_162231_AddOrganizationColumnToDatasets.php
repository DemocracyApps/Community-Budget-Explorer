<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrganizationColumnToDatasets extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('datasets', function(Blueprint $table)
		{
            $table->text('description')->nullable();
			$table->integer('organization');
            $table->foreign('organization')->references('id')->on('organizations');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('datasets', function(Blueprint $table)
		{
            $table->dropColumn(('description'));
            $table->dropColumn(('organization'));
		});
	}

}
