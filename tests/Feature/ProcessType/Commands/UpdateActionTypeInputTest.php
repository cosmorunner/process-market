<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\UpdateActionTypeInput;
use App\ProcessType\Input;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class UpdateActionTypeInputTest
 * @package Tests\Feature\ProcessType\Commands
 */
class UpdateActionTypeInputTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $actionType = app(ActionTypeBuilder::class)->withInputs([Input::make(['value' => 'foobar'])])->make(['name' => 'Dummy']);
        $definition = app(DefinitionBuilder::class)->withActionTypes([$actionType])->make();
        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        /* @var Input $input */
        $input = $definition->actionTypes->first()->inputs->first();

        $this->assertEquals('foobar', $input->value);
        $updatedInputs = array_merge($input->toArray(), ['value' => 'new foobar', 'old_name' => $input->name]);

        $payload = array_merge(['action_type_id' => $actionType->id], $updatedInputs);
        $definition = (new UpdateActionTypeInput($payload, $definition, $processVersion))->execute();

        $this->assertEquals('new foobar', $definition->actionTypes->first()->inputs->first()->value);
    }

}
