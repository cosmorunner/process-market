<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Feature\ProcessType\Commands;

use App\Enums\ProcessRolePermissions;
use App\Models\ProcessVersion;
use App\ProcessType\Commands\DeleteActionType;
use App\ProcessType\Commands\DeleteListConfig;
use App\ProcessType\Commands\DeleteMenuItem;
use App\ProcessType\Commands\DeleteProcessTypeOutput;
use App\ProcessType\Commands\DeleteStatusType;
use Database\Builder\Definition\ActionTypeBuilder;
use Database\Builder\Definition\DefinitionBuilder;
use Database\Builder\Definition\ListConfigBuilder;
use Database\Builder\Definition\MenuItemBuilder;
use Database\Builder\Definition\OutputBuilder;
use Database\Builder\Definition\RoleBuilder;
use Database\Builder\Definition\StatusTypeBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DeletePermissionFromAllRolesTest
 * @package Tests\Feature\ProcessType\Commands
 */
class DeletePermissionFromAllRolesTest extends TestCase {

    use RefreshDatabase;

    public function test_commands_simple() {
        $actionTypeToDelete = app(ActionTypeBuilder::class)->make();
        $statusTypeToDelete = app(StatusTypeBuilder::class)->make();
        $menuItemToDelete = app(MenuItemBuilder::class)->make();
        $listConfigToDelete = app(ListConfigBuilder::class)->make();
        $outputToDelete = app(OutputBuilder::class)->make();

        $executeActionTypeIdent = ident(ProcessRolePermissions::ExecuteActiontype->value, $actionTypeToDelete->id);
        $viewStatusTypeIdent = ident(ProcessRolePermissions::ViewStatustype->value, $statusTypeToDelete->id);
        $viewMenuItemIdent = ident(ProcessRolePermissions::ViewMenuItem->value, $menuItemToDelete->id);
        $viewListConfigIdent = ident(ProcessRolePermissions::ViewListConfig->value, $listConfigToDelete->id);
        $viewOutputIdent = ident(ProcessRolePermissions::ViewOutput->value, $outputToDelete->name);

        $role = app(RoleBuilder::class)
            ->withPermissionIdents([$executeActionTypeIdent, $viewStatusTypeIdent, $viewMenuItemIdent, $viewListConfigIdent, $viewOutputIdent])
            ->make();

        $roleIdents = $role->permissions->pluck('ident');

        $definition = app(DefinitionBuilder::class)
            ->withRoles([$role])
            ->withActionTypes([$actionTypeToDelete])
            ->withStatusTypes([$statusTypeToDelete])
            ->withMenuItems([$menuItemToDelete])
            ->withListConfigs([$listConfigToDelete])
            ->withOutputs([$outputToDelete])
            ->make();

        $processVersion = ProcessVersion::factory()->withDefinition($definition)->create();

        $this->assertTrue($roleIdents->contains($executeActionTypeIdent));
        $this->assertTrue($roleIdents->contains($viewStatusTypeIdent));
        $this->assertTrue($roleIdents->contains($viewMenuItemIdent));
        $this->assertTrue($roleIdents->contains($viewListConfigIdent));
        $this->assertTrue($roleIdents->contains($viewOutputIdent));

        $definition = (new DeleteActionType(['id' => $actionTypeToDelete->id], $definition, $processVersion))->execute();
        $definition = (new DeleteStatusType(['id' => $statusTypeToDelete->id], $definition, $processVersion))->execute();
        $definition = (new DeleteMenuItem(['id' => $menuItemToDelete->id], $definition, $processVersion))->execute();
        $definition = (new DeleteListConfig(['id' => $listConfigToDelete->id], $definition, $processVersion))->execute();
        $definition = (new DeleteProcessTypeOutput(['name' => $outputToDelete->name], $definition, $processVersion))->execute();

        $roleIdents = $definition->roles->first()->permissions->pluck('ident');

        $this->assertFalse($roleIdents->contains($executeActionTypeIdent));
        $this->assertFalse($roleIdents->contains($viewStatusTypeIdent));
        $this->assertFalse($roleIdents->contains($viewMenuItemIdent));
        $this->assertFalse($roleIdents->contains($viewListConfigIdent));
        $this->assertFalse($roleIdents->contains($viewOutputIdent));

    }

}
