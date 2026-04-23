<?php

namespace App\Rules\ProcessType;

use App\Models\Plugin;
use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\ProcessType\Processor;
use App\Rules\ProcessorMaxCount;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class StoreProcessor
 * @package App\Rules\ProcessType
 */
class StoreProcessor implements ValidationRule {

    /**
     * @var ProcessVersion
     */
    private $processVersion;
    /**
     * @var Definition
     */
    private Definition $definition;

    /**
     * @var array
     */
    private $actionTypeIds;

    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
        $this->actionTypeIds = $this->definition->actionTypes->pluck('id');
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $actionTypeId = $value['action_type_id'] ?? null;

        /** @noinspection PhpUndefinedMethodInspection */
        $pluginNamespaces = $this->processVersion->process->author->plugins()
            ->with('latestPublishedVersion')
            ->external()
            ->customProcessors()
            ->enabled()
            ->get()
            ->map(fn(Plugin $plugin) => $plugin->namespace . '/' . $plugin->identifier . '@' . $plugin->latestPublishedVersion->version)
            ->toArray();

        $validator = Validator::make((array) $value, [
            'action_type_id' => ['required', 'uuid', Rule::in($this->actionTypeIds)],
            'identifier' => [
                'required',
                'string',
                Rule::in([...array_keys(Processor::MAX_ITEMS), ...$pluginNamespaces]),
                new ProcessorMaxCount($this->definition, $actionTypeId)
            ],
            'conditions' => ['array'],
            'options' => ['array'],
            'required' => ['boolean'],
            'sort' => ['nullable', 'integer'],
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
