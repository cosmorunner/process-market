<?php

namespace App\Rules\ProcessType;

use App\Models\ProcessVersion;
use App\ProcessType\Definition;
use App\ProcessType\Template;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class UpdateTemplate
 * @package App\Rules\ProcessType
 */
class UpdateTemplate implements ValidationRule {

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
        $validator = Validator::make((array) $value, [
            'id' => ['required', 'uuid', Rule::in($this->definition->templates->pluck('id'))],
            'data' => ['nullable', 'string'],
            'name' => ['required', 'string', 'max:80'],
            'type' => [
                'required',
                'string',
                'in:' . implode(',', [Template::TYPE_HTML, Template::TYPE_CUSTOM_LOGIC, Template::TYPE_MUSTACHE_LIST_COLUMN])
            ],
            'mapping' => ['array']
        ]);

        if (!$validator->passes()) {
            $fail($validator->messages());
        }
    }

}
