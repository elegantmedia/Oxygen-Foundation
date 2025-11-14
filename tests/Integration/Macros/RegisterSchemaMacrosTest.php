<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Integration\Macros;

use ElegantMedia\OxygenFoundation\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RegisterSchemaMacrosTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();
		// Ensure SQLite enforces foreign keys
		DB::statement('PRAGMA foreign_keys = ON');

		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('name')->nullable();
			$table->timestamps();
		});
	}

	protected function tearDown(): void
	{
		Schema::dropIfExists('documents');
		Schema::dropIfExists('users');
		parent::tearDown();
	}

	public function testFileMacroCreatesForeignKeyConstraint(): void
	{
		Schema::create('documents', function (Blueprint $table) {
			$table->id();
			$table->file('document');
			$table->timestamps();
		});

		// Inspect FK metadata via SQLite PRAGMA
		$fks = DB::select('PRAGMA foreign_key_list("documents")');
		$fromColumns = array_map(static fn ($row) => $row->from ?? null, $fks);

		$this->assertContains('document_uploaded_by_user_id', $fromColumns);
	}

	public function testDropFileMacroRemovesColumns(): void
	{
		Schema::create('documents', function (Blueprint $table) {
			$table->id();
			$table->file('document');
			$table->timestamps();
		});

		Schema::table('documents', function (Blueprint $table) {
			$table->dropFile('document');
		});

		$columns = Schema::getColumnListing('documents');

		$this->assertNotContains('document_uploaded_by_user_id', $columns);
		$this->assertNotContains('document_uuid', $columns);
		$this->assertNotContains('document_name', $columns);
	}
}
