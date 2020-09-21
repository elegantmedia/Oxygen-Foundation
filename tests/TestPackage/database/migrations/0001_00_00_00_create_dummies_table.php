<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDummiesTable extends Migration
{
	/**
	 * Run the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dummies', static function (Blueprint $table) {
			$table->id('id');
			$table->string('name')->nullable();
			$table->softDeletes();
			$table->timeStamps();
		});
	}


	/**
	 * Reverse the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('dummies');
	}
}
