<?php

namespace Tests\Unit\Models;

use App\Models\License;
use App\Models\Process;
use App\Models\ProcessLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ProcessLicenseTest
 * @package Tests\Unit\Models
 */
class ProcessLicenseTest extends TestCase {
    use RefreshDatabase;

    public function test_process_license_has_an_id() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->create();
        $this->assertIsString($processLicense->id);
    }

    public function test_process_license_has_a_resource_type() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->create();
        $this->assertEquals(Process::class, $processLicense->resource_type);
    }

    public function test_process_license_has_a_resource_id() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->create();
        $this->assertIsString($processLicense->resource_id);
    }

    public function test_process_license_has_an_owner_type() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->create();
        $this->assertEquals(User::class, $processLicense->owner_type);
    }

    public function test_process_license_has_an_owner_id() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->create();
        $this->assertIsString($processLicense->owner_id);
    }

    public function test_process_license_has_an_issuer_id() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->create();
        $this->assertIsString($processLicense->issuer_id);
    }

    public function test_process_license_has_a_level() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->create();
        $this->assertEquals(License::LEVEL_PRIVATE, $processLicense->level);
    }

    public function test_process_license_has_level_options() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->create();
        $this->assertIsArray($processLicense->level_options);
        $this->assertNotEmpty($processLicense->level_options);
    }

    public function test_process_license_has_create_info() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->create();
        $this->assertNotEmpty($processLicense->created_at);
    }

    public function test_process_license_has_an_issuer() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->withIssuer()->create();
        $this->assertInstanceOf(User::class, $processLicense->issuer);
    }

    public function test_process_license_has_an_owner() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->withOwner(User::factory()->create())->create();
        $this->assertInstanceOf(User::class, $processLicense->owner);
    }

    public function test_process_license_has_a_resource() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->withResource(Process::factory()->create())->create();
        $this->assertInstanceOf(Process::class, $processLicense->resource);
    }

    public function test_process_license_check_if_it_allows_copy() {
        /* @var ProcessLicense $processLicense */
        $processLicense = ProcessLicense::factory()->create(['level_options' => ['allow_copy' => null]]);
        $this->assertFalse($processLicense->allowsCopy());

        $processLicense = ProcessLicense::factory()->create(['level_options' => ['allow_copy' => true]]);
        $this->assertTrue($processLicense->allowsCopy());

        $processLicense = ProcessLicense::factory()->create(['level' => License::LEVEL_OPEN_SOURCE]);
        $this->assertTrue($processLicense->allowsCopy());
    }
}