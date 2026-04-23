<?php

namespace Tests\Unit\Models;

use App\Models\License;
use App\Models\Organisation;
use App\Models\Process;
use App\Models\Synchronization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class LicenseTest
 * @package Tests\Unit\Models
 */
class LicenseTest extends TestCase {

    use RefreshDatabase;

    public function test_license_has_an_id() {
        /* @var License $license */
        $license = License::factory()->create();
        $this->assertIsString($license->id);
    }

    public function test_license_has_a_resource_type() {
        /* @var License $license */
        $license = License::factory()->create();
        $this->assertEquals(User::class, $license->resource_type);
    }

    public function test_license_has_a_resource_id() {
        /* @var License $license */
        $license = License::factory()->create();
        $this->assertIsString($license->resource_id);
    }

    public function test_license_has_a_owner_type() {
        /* @var License $license */
        $license = License::factory()->create();
        $this->assertEquals(Organisation::class, $license->owner_type);
    }

    public function test_license_has_an_owner_id() {
        /* @var License $license */
        $license = License::factory()->create();
        $this->assertIsString($license->owner_id);
    }

    public function test_license_has_an_issuer_id() {
        /* @var License $license */
        $license = License::factory()->create();
        $this->assertIsString($license->issuer_id);
    }

    public function test_license_has_a_level() {
        /* @var License $license */
        $license = License::factory()->create();
        $this->assertIsString($license->level);
    }

    public function test_license_has_level_options() {
        /* @var License $license */
        $license = License::factory()->create();
        $this->assertIsString($license->level_options[0]);
    }

    public function test_license_has_create_info() {
        /* @var License $license */
        $license = License::factory()->create();
        $this->assertNotEmpty($license->created_at);
    }

    public function test_license_has_resource() {
        /* @var License $license */
        $license = License::factory()->withResource()->create();
        $this->assertInstanceOf(Process::class, $license->resource);
    }

    public function test_license_has_an_owner() {
        /* @var License $license */
        $license = License::factory()->withOwner()->create();
        $this->assertInstanceOf(User::class, $license->owner);
    }

    public function test_license_has_synchronizations() {
        /* @var License $license */
        $license = License::factory()->withSynchronization()->create();
        $this->assertInstanceOf(Synchronization::class, $license->synchronizations[0]);
    }

    public function test_license_has_an_issuer() {
        /* @var License $license */
        $license = License::factory()->withIssuer()->create();
        $this->assertInstanceOf(User::class, $license->issuer);
    }

    public function test_license_owner_has_licensepath() {
        /* @var License $license */
        $license = License::factory()->withOwner()->create();
        $this->assertIsString($license->ownerProfileLicensesPath());
    }

    public function test_license_is_owned_by_a_user() {
        /* @var License $license */
        /* @var User $user */
        $user = User::factory()->create();
        $license = License::factory()->withOwner($user)->create();
        $this->assertTrue($license->ownedByUser());
    }

    public function test_license_is_owned_by_an_organisation() {
        /* @var License $license */
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $license = License::factory()->withOwner($organisation)->create();
        $this->assertTrue($license->ownedByOrganisation());
    }

    public function test_license_is_open_source() {
        /* @var License $license */
        /** @noinspection PhpRedundantOptionalArgumentInspection */
        $license = License::factory()->withLevel(License::LEVEL_OPEN_SOURCE)->create();
        $this->assertTrue($license->isOpenSource());
    }

    public function test_license_get_level() {
        /* @var License $license */
        $license = License::factory()->withLevel(License::LEVEL_PRIVATE)->create();
        $this->assertEquals('Privat', $license->typeTitle());
    }

    public function test_license_get_scope_of_owner() {
        /* @var License $license */
        /* @var User $owner */
        $owner = User::factory()->create();
        $license = License::factory()->withOwner($owner)->create();
        $queryLicense = License::scopeOfOwner(License::query(), $license->owner)->first();
        $this->assertEquals($license->id, $queryLicense->id);
    }

    public function test_license_can_be_identified() {
        /* @var License $createLicense */
        /* @var License $license */
        /* @var User $owner */
        /* @var Process $resource */
        $user = User::factory()->create();
        $resource = Process::factory()->create();
        $createLicense = License::factory()
            ->withOwner($user)
            ->withResource($resource)
            ->create();

        $license = License::identify($createLicense->resource, $createLicense->owner);
        $this->assertEquals($createLicense->id, $license->id);
    }
}
