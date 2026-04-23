<?php


namespace App\Environment;

use Illuminate\Support\Collection;

/**
 * Class Blueprint
 * @package App\Environment
 */
class Blueprint extends AbstractModel {

    public ?Collection $users = null;
    public ?Collection $bots = null;
    public ?Collection $groups = null;
    public ?Collection $processes = null;
    public ?Collection $relations = null;
    public ?Collection $connectors = null;
    public ?Collection $requests = null;
    public ?Collection $groupAccesses = null;
    public ?Collection $systemAccesses = null;
    public ?Collection $settings = null;
    public ?Collection $groupRoles = null;
    public ?Collection $publicApis = null;
    public ?Collection $variables = null;
    public ?Collection $tasks = null;

    /**
     * Eigenschaften die eine Collection repräsentieren.
     * @var array
     */
    public $collections = [
        'users' => User::class,
        'bots' => Bot::class,
        'groups' => Group::class,
        'processes' => Process::class,
        'relations' => Relation::class,
        'connectors' => Connector::class,
        'requests' => Request::class,
        'groupAccesses' => GroupAccess::class,
        'systemAccesses' => SystemAccess::class,
        'settings' => Setting::class,
        'groupRoles' => GroupRole::class,
        'publicApis' => PublicApi::class,
        'variables' => Variable::class,
        'tasks' => Task::class
    ];

    /**
     * Neuer Standard-Blueprint
     * @param array $options
     * @return Blueprint
     */
    public static function make($options = []) {
        return new self([
            'users' => $options['users'] ?? collect(),
            'bots' => $options['bots'] ?? collect(),
            'groups' => $options['groups'] ?? collect(),
            'processes' => $options['processes'] ?? collect(),
            'relations' => $options['relations'] ?? collect(),
            'connectors' => $options['connectors'] ?? collect(),
            'requests' => $options['requests'] ?? collect(),
            'groupAccesses' => $options['group_accesses'] ?? collect(),
            'systemAccesses' => $options['system_accesses'] ?? collect(),
            'settings' => $options['settings'] ?? collect(),
            'groupRoles' => $options['group_roles'] ?? collect(),
            'publicApis' => $options['public_apis'] ?? collect(),
            'variables' => $options['variables'] ?? collect(),
            'tasks' => $options['tasks'] ?? collect()
        ]);
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            'users' => ($this->users ?? collect())->map(fn(User $user) => $user->toArray())->values()->toArray(),
            'bots' => ($this->bots ?? collect())->map(fn(Bot $bot) => $bot->toArray())->values()->toArray(),
            'groups' => ($this->groups ?? collect())->map(fn(Group $group) => $group->toArray())->values()->toArray(),
            'processes' => ($this->processes ?? collect())->map(fn(Process $process) => $process->toArray())->values()->toArray(),
            'relations' => ($this->relations ?? collect())->map(fn(Relation $relation) => $relation->toArray())->values()->toArray(),
            'connectors' => ($this->connectors ?? collect())->map(fn(Connector $connector) => $connector->toArray())->values()->toArray(),
            'requests' => ($this->requests ?? collect())->map(fn(Request $request) => $request->toArray())->values()->toArray(),
            'group_accesses' => ($this->groupAccesses ?? collect())->map(fn(GroupAccess $groupAccess) => $groupAccess->toArray())->values()->toArray(),
            'system_accesses' => ($this->systemAccesses ?? collect())->map(fn(SystemAccess $systemAccess) => $systemAccess->toArray())->values()->toArray(),
            'settings' => ($this->settings ?? collect())->map(fn(Setting $systemSetting) => $systemSetting->toArray())->values()->toArray(),
            'group_roles' => ($this->groupRoles ?? collect())->map(fn(GroupRole $groupRole) => $groupRole->toArray())->values()->toArray(),
            'public_apis' => ($this->publicApis ?? collect())->map(fn(PublicApi $publicApi) => $publicApi->toArray())->values()->toArray(),
            'variables' => ($this->variables ?? collect())->map(fn(Variable $variable) => $variable->toArray())->values()->toArray(),
            'tasks' => ($this->tasks ?? collect())->map(fn(Task $task) => $task->toArray())->values()->toArray(),
        ];
    }
}
