<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Rules\ProcessType;

use App\Models\Process;
use App\Models\ProcessVersion;
use App\Rules\ProcessType\StoreState;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\StatusTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\MessageBag;
use Tests\TestCase;

/**
 * Class StoreStateTest
 * @package Tests\Unit\Rules\ProcessType
 */
class StoreStateTest extends TestCase {

    use RefreshDatabase;

    public function test_store_state_rule_missing_description() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->withAnyStatusTypes()->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        request()->merge(['processVersion' => $processVersion]);

        $payload = [
            'status_type_id' => $definition->statusTypes->first()->id,
            'description' => '',
            'min' => '1',
            'max' => '2',
        ];

        // We assert that the validation rule has a validation message for "min" and max.
        (new StoreState($payload))->validate('payload', $payload, function (MessageBag $errorBag) {
            $this->assertTrue($errorBag->has(['description']));
        });
    }

    public function test_store_state_rule_invalid_min_max() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->withAnyStatusTypes()->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        // Make sure that the request has a "processVersion" model set.
        request()->merge(['processVersion' => $processVersion]);

        $payload = [
            'status_type_id' => $definition->statusTypes->first()->id,
            'description' => 'Test',
            'min' => 'abc',
            'max' => 'def',
        ];

        // We assert that the validation rule has a validation message for "min" and max.
        (new StoreState($payload))->validate('payload', $payload, function (MessageBag $errorBag) {
            $this->assertTrue($errorBag->has(['min', 'max']));
        });

        $payload = [
            'status_type_id' => $definition->statusTypes->first()->id,
            'description' => 'Test',
            'min' => '',
            'max' => '',
        ];

        // We assert that the validation rule has a validation message for "min" and max.
        (new StoreState($payload))->validate('payload', $payload, function (MessageBag $errorBag) {
            $this->assertTrue($errorBag->has(['min', 'max']));
        });
    }

    public function test_store_state_rule_max_is_not_required() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->withAnyStatusTypes()->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        // We set this flag to indicate, that no assertions must be made.
        $this->expectNotToPerformAssertions();

        // Make sure that the request has a "processVersion" model set.
        request()->merge(['processVersion' => $processVersion]);

        $payload = [
            'status_type_id' => $definition->statusTypes->first()->id,
            'min' => '11',
            'description' => 'Test'
        ];

        // We assert that the validation rule did not trigger a validation message for "max".
        (new StoreState($payload))->validate('payload', $payload, function (MessageBag $errorBag) {
            $this->assertEmpty($errorBag->all());
        });
    }

    public function test_store_state_rule_min_is_missing() {
        $process = Process::factory()->ofCreatorAndAuthor($this->login())->create();
        $definition = app(DefinitionBuilder::class)->withAnyStatusTypes()->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->ofProcess($process)->create();

        // Make sure that the request has a "processVersion" model set.
        request()->merge(['processVersion' => $processVersion]);

        $payload = [
            'status_type_id' => $definition->statusTypes->first()->id,
            'max' => '11',
            'description' => 'Test'
        ];

        // We assert that the validation rule triggered a validation message for "min".
        (new StoreState($payload))->validate('payload', $payload, function (MessageBag $errorBag) {
            $this->assertTrue($errorBag->has('min'));
        });
    }

    public function test_store_state_rule_no_encasing_of_an_existing_state() {
        // Statustype with a state min: 0, max: 0.
        $statusType = app(StatusTypeBuilder::class)->withStates([0])->make();
        $definition = app(DefinitionBuilder::class)->withStatusTypes([$statusType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        // Make sure that the request has a "processVersion" model set.
        request()->merge(['processVersion' => $processVersion]);

        $payload = [
            'status_type_id' => $statusType->id,
            'min' => '-10',
            'max' => '10',
            'description' => 'Test',
        ];

        // We assert that the validation rule triggered a validation message for "max".
        (new StoreState($payload))->validate('payload', $payload, function (MessageBag $errorBag) {
            $this->assertTrue($errorBag->has('max'));
        });

        $payload = [
            'status_type_id' => $statusType->id,
            'min' => '10',
            'max' => '20',
            'description' => 'Test',
        ];

        // Callback should not be called. In case it is called, the error bag should be empty.
        (new StoreState($payload))->validate('payload', $payload, function (MessageBag $errorBag) {
            $this->assertEmpty($errorBag->all());
        });
    }
}
