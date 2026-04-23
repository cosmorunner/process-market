<?php

namespace App\Rules\Environment;

use App\Environment\Bot;
use App\Environment\Connector;
use App\Environment\User;
use App\Models\Environment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateTask
 * @package App\Rules\Environment
 */
class UpdateTask implements ValidationRule {

    private Environment $environment;

    /**
     * UpdateTask constructor.
     */
    public function __construct() {
        $this->environment = request('environment');
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $taskIds = $this->environment->blueprint->tasks->pluck('id');

        /* @var Connector $currentTask */
        $currentTask = $this->environment->blueprint->tasks->firstWhere('id', '=', $value['id'] ?? '');
        $taskVariables = $this->environment->blueprint->tasks->pluck('identifier');

        if ($currentTask) {
            $taskVariables = $taskVariables->filter(fn($identifer) => $identifer != $currentTask->identifier);
        }

        $userIdentifiers = [];

        /* @var User $user */
        foreach ($this->environment->blueprint->users as $user) {
            $userIdentifiers = array_merge($userIdentifiers, $user->aliases);
        }

        /* @var Bot $bot */
        foreach ($this->environment->blueprint->bots as $bot) {
            $userIdentifiers = array_merge($userIdentifiers, $bot->aliases);
        }

        $pipeNotations = array_map(fn($item) => 'user|' . $item, $userIdentifiers);

        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid', Rule::in($taskIds)],
            'identifier' => ['required', 'string', 'max:60', Rule::notIn($taskVariables)],
            'user' => ['required', 'string', 'max:60', Rule::in($pipeNotations)]

        ], [], [
            'identifier' => 'Identifier',
            'user' => 'Bot'
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
