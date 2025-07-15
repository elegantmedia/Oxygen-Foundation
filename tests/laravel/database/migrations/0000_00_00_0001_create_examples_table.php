<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExamplesTable extends Migration
{
    /**
     * Run the database.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examples', static function (Blueprint $table) {
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
        Schema::drop('examples');
    }
}
