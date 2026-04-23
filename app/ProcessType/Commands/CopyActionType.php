<?php

namespace App\ProcessType\Commands;

use App\ProcessType\ActionRule;
use App\ProcessType\ActionType;
use App\ProcessType\ActionTypeComponent;
use App\ProcessType\Definition;
use App\ProcessType\Permission;
use App\ProcessType\Processor;
use App\ProcessType\Role;
use App\ProcessType\StatusRule;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Class CopyActionType
 * @package App\ProcessType\Commands
 */
class CopyActionType extends Command {

    /**
     * Definition array keys that are updated by the command.
      Only every key is returned after the command, for improved performance.
      If empty, everything is returned.
     * @var array
     */
    const AFFECTS_PARTS = ['action_types', 'roles'];

    /**
     * The new id for the copied action.
     * @var string
     */
    private string $copiedActionTypeId;

    public $recalculate = true;

    /**
     * Executes a command. Is used to edit the graph definition.
     * @return mixed
     */
    public function command(): Definition {
        $actionTypeId = $this->payload['id'];
        $actionType = $this->definition->actionType($actionTypeId);
        $categoryActionTypes = $this->definition->actionTypes->where('category_id', '=', $actionType->category_id);
        $existingNames = $this->definition->actionTypes->pluck('name');
        $existingReferences = $this->definition->actionTypes->pluck('reference');

        // Copy action type.
        $copiedActionType = new ActionType($actionType->toArray());
        $copiedActionTypeId = Uuid::uuid4()->toString();
        $copiedActionType->id = $copiedActionTypeId;

        // There is a chances the new name already exists
        $retryCount = 0;
        do {
            $name = $copiedActionType->name . ' - Kopie';
            if ($retryCount > 1) {
                $name .= " ({$retryCount})";
            }
            $retryCount++;
        } while ($existingNames->contains($name));

        $copiedActionType->name = $name;

        // There is a chances the reference already exists
        $retryCount = 0;
        do {
            $reference = $copiedActionType->reference . '_kopie';
            if ($retryCount > 1) {
                $reference .= "_{$retryCount}";
            }

            $retryCount++;
        } while ($existingReferences->contains($reference));
        $copiedActionType->reference = $reference;

        $copiedActionType->sort = $categoryActionTypes->max('sort') + 1;

        $copiedActionType->actionRules = $copiedActionType->actionRules->map(function (ActionRule $actionRule) use ($copiedActionTypeId) {
            $actionRule->id = Str::uuid()->toString();
            $actionRule->action_type_id = $copiedActionTypeId;

            return $actionRule;
        });

        $copiedActionType->statusRules = $copiedActionType->statusRules->map(function (StatusRule $statusRule) use ($copiedActionTypeId) {
            $statusRule->id = Str::uuid()->toString();
            $statusRule->action_type_id = $copiedActionTypeId;

            return $statusRule;
        });

        $copiedActionType->processors = $copiedActionType->processors->map(function (Processor $processor) use ($copiedActionTypeId) {
            $processor->action_type_id = $copiedActionTypeId;

            return $processor;
        });

        $copiedActionType->components = $copiedActionType->components->map(function (ActionTypeComponent $component) use ($copiedActionTypeId) {
            $component->action_type_id = $copiedActionTypeId;

            return $component;
        });

        // Copy also action type role's permissions to the copied action type.
        $this->definition->roles->map(function (Role $role) use ($actionTypeId, $copiedActionTypeId) {
            collect(config('permissions.action_type'))->map(function ($template) use ($role, $actionTypeId, $copiedActionTypeId) {
                $permission = $role->permissions->firstWhere('ident', '=', Permission::templateToIdent($template['ident'], $actionTypeId));

                if ($permission) {
                    $role->permissions->add(Permission::make([
                        'name' => $permission->name,
                        'description' => $permission->description,
                        'ident' => Permission::templateToIdent($template['ident'], $copiedActionTypeId),
                    ]));
                }
            });
        });

        $this->definition->actionTypes->add($copiedActionType);

        $this->copiedActionTypeId = $copiedActionTypeId;

        return $this->definition;
    }

    /**
     * @param array $position
     * @param array $newCalculated
     * @return array
     */
    public function updateGraphPositions(array $position, array $newCalculated) {
        // Get action types positions that we copy them along with their status types ids.
        $actionTypeId = $this->payload['id'];
        $rawNodes = collect($this->processVersion->calculated);
        $actionTypeNodes = $rawNodes->where('data.type', '=', 'action')->where('data.model_id', '=', $actionTypeId);
        $positions = [];

        foreach ($actionTypeNodes as $actionTypeNode) {
            $actionTypeNodePosition = $actionTypeNode['position'] ?? [];

            if (!empty($actionTypeNodePosition)) {
                $positions[] = [
                    'status_type_id' => $actionTypeNode['data']['status_type_id'],
                    'x' => $actionTypeNodePosition['x'],
                    'y' => $actionTypeNodePosition['y'],
                ];
            }
        }

        foreach ($newCalculated as $key => $item) {
            $data = $item['data'];
            $modelId = $data['model_id'] ?? null;

            if ($modelId && $modelId === $this->copiedActionTypeId && $data['type'] === 'action') {
                $position = collect($positions)->firstWhere('status_type_id', '=', $data['status_type_id']);

                if ($position) {
                    $item['position']['x'] = $position['x'] + rand(-75, 75);
                    $item['position']['y'] = $position['y'] + rand(-75, 75);
                }

                $newCalculated[$key] = $item;
            }
        }

        return $newCalculated;
    }

}
