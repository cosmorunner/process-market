<?php

namespace Tests\Unit\Models;

use App\Models\ProcessVersion;
use App\Models\ProcessVersionHistory;
use App\ProcessType\Definition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ProcessVersionHistoryTest
 * @package Tests\Unit\Models
 */
class ProcessVersionHistoryTest extends TestCase {

    use RefreshDatabase;

    public function test_process_version_history_has_an_id() {
        /* @var ProcessVersionHistory $processVersionHistory */
        $processVersionHistory = ProcessVersionHistory::factory()->create();
        $this->assertIsString($processVersionHistory->id);
    }

    public function test_process_version_history_has_a_process_versions_id() {
        /* @var ProcessVersionHistory $processVersionHistory */
        $processVersionHistory = ProcessVersionHistory::factory()->create();
        $this->assertIsString($processVersionHistory->process_version_id);
    }

    public function test_process_version_history_has_a_command() {
        /* @var ProcessVersionHistory $processVersionHistory */
        $processVersionHistory = ProcessVersionHistory::factory()->create();
        $this->assertIsString($processVersionHistory->command);
    }

    public function test_process_version_history_has_a_command_payload() {
        /* @var ProcessVersionHistory $processVersionHistory */
        $processVersionHistory = ProcessVersionHistory::factory()->create();
        $this->assertIsArray($processVersionHistory->command_payload);
    }

    public function test_process_version_history_has_calculated() {
        /* @var ProcessVersionHistory $processVersionHistory */
        $processVersionHistory = ProcessVersionHistory::factory()->create();
        $this->assertIsArray($processVersionHistory->calculated);
    }

    public function test_process_version_history_has_a_definition() {
        /* @var ProcessVersionHistory $processVersionHistory */
        $processVersionHistory = ProcessVersionHistory::factory()->create();
        $this->assertInstanceOf(Definition::class, $processVersionHistory->definition);
    }

    public function test_process_version_history_has_created_info() {
        /* @var ProcessVersionHistory $processVersionHistory */
        $processVersionHistory = ProcessVersionHistory::factory()->create();
        $this->assertNotNull($processVersionHistory->created_at);
    }

    public function test_process_version_history_has_update_info() {
        /* @var ProcessVersionHistory $processVersionHistory */
        $processVersionHistory = ProcessVersionHistory::factory()->create();
        $processVersionHistory->command = "test";
        $processVersionHistory->save();
        $this->assertNotNull($processVersionHistory->updated_at);
    }

    public function test_process_version_history_has_process_version() {
        /* @var ProcessVersionHistory $processVersionHistory */
        /* @var ProcessVersion $processVersion */
        $processVersion = ProcessVersion::factory()->create();
        $processVersionHistory = ProcessVersionHistory::factory()->ofProcessVersion($processVersion)->create();
        $this->assertInstanceOf(Processversion::class, $processVersionHistory->processVersion);
        $this->assertEquals($processVersion->id, $processVersionHistory->processVersion->id);
    }

    public function test_process_version_history_with_no_command() {
        /* @var ProcessVersionHistory $processVersionHistory */
        /* @var ProcessVersion $processVersion */
        $processVersion = ProcessVersion::factory()->create();
        $processVersionHistory = ProcessVersionHistory::makeInitial($processVersion);
        $this->assertNull($processVersionHistory->command);
        $this->assertNull($processVersionHistory->command_payload);
    }

    public function test_process_version_history_get_definition_attribute() {
        /* @var ProcessVersionHistory $processVersionHistory */
        /* @var Definition $definition */
        $processVersionHistory = ProcessVersionHistory::factory()->create();
        $definition = $processVersionHistory->definition;
        $this->assertNotEmpty($definition);
    }

    public function test_process_version_history_get_raw_definition() {
        /* @var ProcessVersionHistory $processVersionHistory */
        /* @var array $definition */
        $processVersionHistory = ProcessVersionHistory::factory()->create();
        $definition = $processVersionHistory->getRawDefintion();
        $this->assertTrue(is_array($definition));
        $this->assertNotEmpty($definition);
    }

    public function test_process_version_history_make_with_command() {
        /* @var ProcessVersionHistory $processVersionHistory */
        /* @var ProcessVersion $processVersion */
        $processVersion = ProcessVersion::factory()->create();
        $processVersionHistory = ProcessVersionHistory::makeWithCommand($processVersion, 'commandName', ['commandPayload' => 'payload1']);
        $this->assertEquals('commandName', $processVersionHistory->command);
        $this->assertEquals('payload1', $processVersionHistory->command_payload['commandPayload']);
    }
}