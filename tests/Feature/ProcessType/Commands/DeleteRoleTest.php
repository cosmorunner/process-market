<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteRole;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ProcessorBuilder;
use Database\Builder\Definition\RoleBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeleteRoleTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeleteRoleTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $role = app(RoleBuilder::class)->make();
        $role2 = app(RoleBuilder::class)->make();
        $processor = app(ProcessorBuilder::class)->make([
            'identifier' => 'create_access',
            'options' => ['role' => pipe_notation($role)]
        ]);

        $actionTypeWithProcessor = app(ActionTypeBuilder::class)->withProcessors([$processor])->make();

        $this->assertEquals($actionTypeWithProcessor->processors->first()->options['role'], pipe_notation($role));

        $definition = app(DefinitionBuilder::class)
            ->withActionTypes([$actionTypeWithProcessor])
            ->withRoles([$role, $role2])
            ->make();

        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $payload = ['id' => $role->id];

        $this->assertCount(2, $definition->roles);
        $definition = (new DeleteRole($payload, $definition, $processVersion))->execute();
        $this->assertCount(1, $definition->roles);

        // Durch das Löschen der Rolle muss nun auch die Rolle aus dem Prozessor entfernt worden sein.
        $actionTypeWithProcessor = $definition->actionType($actionTypeWithProcessor->id);
        $this->assertNull($actionTypeWithProcessor->processors->first()->options['role']);
    }

}
