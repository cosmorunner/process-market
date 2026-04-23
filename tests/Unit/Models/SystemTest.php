<?php

namespace Tests\Unit\Models;

use App\Models\Organisation;
use App\Models\ProcessVersion;
use App\Models\Synchronization;
use App\Models\System;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class SystemTest
 * @package Tests\Unit\Models
 */
class SystemTest extends TestCase {

    use RefreshDatabase;

    public function test_it_has_a_name() {
        /* @var System $system */
        $system = System::factory()->create();
        $this->assertIsString($system->name);
    }

    public function test_it_has_an_url() {
        /* @var System $system */
        $system = System::factory()->create();
        $this->assertIsString($system->url);
    }

    public function test_it_has_a_client_id() {
        /* @var System $system */
        $system = System::factory()->create();
        $this->assertIsString($system->client_id);
    }

    public function test_it_has_a_client_secret() {
        /* @var System $system */
        $system = System::factory()->create();
        $this->assertIsString($system->client_secret);
    }

    public function test_it_has_a_token() {
        /* @var System $system */
        $system = System::factory()->create();
        $this->assertIsString($system->token);
    }

    public function test_it_has_an_owner() {
        /* @var System $system */
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->create();
        $system = System::factory()->ofOwner($organisation)->create();
        $this->assertInstanceOf(Organisation::class, $system->owner);
    }

    public function test_it_has_synchronizations() {
        /* @var System $system */
        $system = System::factory()->withSynchronization()->create();

        $this->assertInstanceOf(Collection::class, $system->synchronizations);
        $this->assertNotEmpty($system->synchronizations);
    }

    public function test_it_can_return_all_synchronizations_of_a_resource() {
        /* @var System $system */
        /* @var Synchronization $synchronization */
        /* @var ProcessVersion $processVersion */
        $system = System::factory()->withSynchronization()->create();
        $processVersion = ProcessVersion::factory()->create();
        $synchronization = Synchronization::factory()->toSystem($system)->ofSubject($processVersion)->create();

        $graphSynchronizations = $synchronization->system->synchronizationsOf($processVersion);

        $this->assertInstanceOf(Collection::class, $graphSynchronizations);
        $this->assertNotEmpty($graphSynchronizations);
    }
}
