<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScriptsToSitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->boolean('live')->default(false);
			$table->text('scripts')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sites', function(Blueprint $table)
		{
			$table->dropColumn('live');
			$table->dropColumn('scripts');
		});
	}

}
