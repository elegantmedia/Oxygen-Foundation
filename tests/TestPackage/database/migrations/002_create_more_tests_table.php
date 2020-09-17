<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMoreTestsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('more_tests', static function(Blueprint $table)
		{
			$table->id('id');
			$table->string('name')->nullable();
			$table->softDeletes();
			$table->timeStamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('more_tests');
	}
}
