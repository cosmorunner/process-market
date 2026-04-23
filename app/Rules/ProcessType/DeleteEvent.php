<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class DeleteEvent
 * @package App\Rules\ProcessType
 */
class DeleteEvent implements ValidationRule {

    /**
     * @var ProcessVersion
     */
    private $processVersion;

    /**
     * @var Definition
     */
    private Definition $definition;

    public function __construct() {
        $this->processVersion = request('processVersion');
        $this->definition = $this->processVersion->definition;
    }

    /**
     * Determine if the validation rule passes.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $events = $this->definition->events;
        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid', Rule::in($events->pluck('id'))],
            'name' => ['required', 'string']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
