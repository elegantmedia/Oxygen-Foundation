<?php

declare(strict_types=1);

namespace ElegantMedia\OxygenFoundation\Tests\Integration\Database\Eloquent\Traits;

use ElegantMedia\OxygenFoundation\Contracts\HasUuid as HasUuidContract;
use ElegantMedia\OxygenFoundation\Database\Eloquent\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;

class UuidContractModel extends Model implements HasUuidContract
{
    use HasUuid;

    protected $table = 'uuid_contract_models';

    protected $fillable = ['uuid', 'name'];
}

class HasUuidContractIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('uuid_contract_models', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('uuid_contract_models');
        parent::tearDown();
    }

    public function testTraitImplementsContractMethods(): void
    {
        $model = new UuidContractModel();
        $this->assertSame('uuid', $model->getUuidColumn());
    }

    public function testFindByUuidReturnsModelOrNull(): void
    {
        $created = UuidContractModel::create(['name' => 'Foo']);
        $uuid = $created->uuid; // set in creating hook

        $found = UuidContractModel::findByUuid($uuid);
        $this->assertNotNull($found);
        $this->assertSame($uuid, $found->uuid);

        $this->assertNull(UuidContractModel::findByUuid('non-existent-uuid'));
    }
}

