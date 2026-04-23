<?php

namespace App\ProcessType\Commands;

use App\ProcessType\Definition;
use Database\Builder\Definition\RoleBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class CopyRole
 * @package App\ProcessType\Commands
 */
class CopyRole extends Command {

    /**
     * Definition array keys that are updated by the command.
     * Only every key is returned after the command, for improved performance.
     * If empty, everything is returned.
     * @var array
     */
    const AFFECTS_PARTS = ['roles'];

    public $recalculate = true;

    /**
     * Executes a command. Is used to edit the graph definition.
     * @return mixed
     * @noinspection PhpUnhandledExceptionInspection
     * @throws BindingResolutionException
     */
    public function command(): Definition {
        $roleId = $this->payload['id'];
        $role = $this->definition->role($roleId);

        $copiedRole = app(RoleBuilder::class)->withPermissions($role->permissions->toArray())->make([
            'name' => $role->name . ' - Kopie',
            'description' => $role->description,
        ]);

        $this->definition->roles->add($copiedRole);

        return $this->definition;
    }

    /**
     * @param array $position
     * @param array $newCalculated
     * @return array
     */
    public function updateGraphPositions(array $position, array $newCalculated) {
        return $newCalculated;
    }
}