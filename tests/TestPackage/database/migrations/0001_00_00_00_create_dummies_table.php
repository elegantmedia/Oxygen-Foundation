<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDummiesTable extends Migration
{
	/**
	 * Run the database.
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
	 */
	public function down()
	{
		Schema::drop('dummies');
	}
}
