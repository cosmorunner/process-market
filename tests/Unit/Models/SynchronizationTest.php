<?php

namespace Tests\Unit\Models;

use App\Models\ProcessVersion;
use App\Models\Synchronization;
use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class SynchronizationTest
 * @package Tests\Unit\Models
 */
class SynchronizationTest extends TestCase {

    use RefreshDatabase;

    public function test_it_has_a_system() {
        /* @var Synchronization $synchronization */
        /* @var System $system */
        $system = System::factory()->create();
        $synchronization = Synchronization::factory()->toSystem($system)->create();
        $this->assertInstanceOf(System::class, $synchronization->system);
    }

    public function test_it_has_a_subject() {
        /* @var Synchronization $synchronization */
        /* @var ProcessVersion $processVersion */
        $processVersion = ProcessVersion::factory()->create();
        $synchronization = Synchronization::factory()->ofSubject($processVersion)->create();
        $this->assertInstanceOf(ProcessVersion::class, $synchronization->subject);
    }

    public function test_it_has_a_user() {
        /* @var Synchronization $synchronization */
        /* @var User $user */
        $user = User::factory()->create();
        $synchronization = Synchronization::factory()->byUser($user)->create();
        $this->assertInstanceOf(User::class, $synchronization->user);
    }

    public function test_it_can_check_whether_it_was_a_success() {
        /* @var Synchronization $synchronization */
        $synchronization = Synchronization::factory()->success()->create();
        $this->assertTrue($synchronization->isSuccess());
    }

    public function test_it_can_check_whether_it_was_a_failure() {
        /* @var Synchronization $synchronization */
        $synchronization = Synchronization::factory()->failure()->create();
        $this->assertTrue($synchronization->isFailure());
    }
}
